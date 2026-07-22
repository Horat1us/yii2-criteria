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

        // Only fields present in selectKeys (as a value) are selected as plain columns; anything else is dropped
        yield [
            $connection,
            ['ColumnA', 'ColumnB', 'ColumnC'],
            ['ColumnA', 'ColumnB'],
            ['qColumnA' => 'qColumnA', 'qColumnB' => 'qColumnB'],
        ];

        // A selectKeys entry keyed by field name is a server-authored expression, aliased back
        // to the client-requested field name
        yield [
            $connection,
            ['alias'],
            ['alias' => 'table.real_column'],
            ['qalias' => 'table.real_column'],
        ];

        // Nothing requested matches selectKeys: everything is dropped, select() gets an empty
        // array, which Yii's query builder treats as "SELECT *" - same as if this criterion had
        // never been applied at all, never as "select nothing"
        yield [
            $connection,
            ['ColumnC'],
            ['ColumnA', 'ColumnB'],
            [],
        ];
    }

    /**
     * @dataProvider applyProvider
     */
    public function testApplyQuery(
        db\Connection $connection,
        array $fields,
        array $selectKeys,
        array $expectedSelect
    ): void {
        $criteria = new SelectCriteria($connection);
        $criteria->fields = $fields;
        $criteria->selectKeys = $selectKeys;

        $resultQuery = $criteria->apply(new db\Query);
        $this->assertEquals($expectedSelect, $resultQuery->select);
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
