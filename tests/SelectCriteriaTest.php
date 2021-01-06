<?php declare(strict_types=1);

namespace Horat1us\Yii\Criteria\Tests;

use Horat1us\Yii\Criteria\SelectCriteria;
use PHPUnit\Framework\TestCase;
use yii\db;

class SelectCriteriaTest extends TestCase
{
    public function applyProvider(): iterable
    {
        $connection = $this->mockConnection();
        $fields = ['ColumnA', 'ColumnB'];
        $expectedSelectColumns = ['qColumnA', 'qColumnB'];
        // Simple quoting columns
        yield [$connection, $fields, new db\Query, $expectedSelectColumns];

        // Filtering and quoting columns when db\ActiveQuery used
        $record = new class extends db\ActiveRecord {
            public function attributes(): array
            {
                return ['ColumnA', 'ColumnB'];
            }
        };

        yield [
            $connection,
            [...$fields, 'ColumnC'], // our record mock does not contain ColumnC attribute, so it must be excluded
            new db\ActiveQuery(get_class($record)),
            $expectedSelectColumns
        ];
    }

    /**
     * @dataProvider applyProvider
     */
    public function testApplyQuery(
        db\Connection $connection,
        array $fields,
        db\Query $query,
        array $expectedSelectColumns
    ): void {
        $criteria = new SelectCriteria($connection);
        $criteria->fields = $fields;

        $resultQuery = $criteria->apply($query);
        $this->assertSame($query, $resultQuery);

        $this->assertEquals(array_combine($expectedSelectColumns, $expectedSelectColumns), $resultQuery->select);
    }

    public function validationProvider(): array
    {
        $connection = $this->createPartialMock(db\Connection::class, []);
        return [
            [$connection, [1,2,3], false,],
            [$connection, [], false,],
            [$connection, ['col1', 'col2'], true,],
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidation(db\Connection $connection, array $fields, bool $expectedResult): void
    {
        $criteria = new SelectCriteria($connection);
        $criteria->fields = $fields;
        $this->assertEquals($expectedResult, $criteria->validate());
    }

    private function mockConnection(): db\Connection
    {
        $schema = $this->createPartialMock(db\sqlite\Schema::class, ['quoteSimpleColumnName']);
        $schema
            ->expects($this->atLeastOnce())
            ->method('quoteSimpleColumnName')
            ->willReturnCallback(fn(string $input) => "q{$input}");

        $connection = $this->createPartialMock(db\Connection::class, ['getSchema']);
        $connection
            ->expects($this->atLeastOnce())
            ->method('getSchema')
            ->willReturn($schema);

        return $connection;
    }
}
