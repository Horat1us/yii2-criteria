<?php declare(strict_types=1);

namespace Horat1us\Yii\Criteria\Tests;

use Horat1us\Yii\Criteria\SelectCriteria;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use yii\db;

class SelectCriteriaTest extends TestCase
{
    public static function applyProvider(): iterable
    {
        // Only fields present in selectKeys (as a value) are selected as plain columns; anything else is dropped
        yield [
            ['ColumnA', 'ColumnB', 'ColumnC'],
            ['ColumnA', 'ColumnB'],
            ['qColumnA' => 'qColumnA', 'qColumnB' => 'qColumnB'],
        ];

        // A selectKeys entry keyed by field name is a server-authored expression, aliased back
        // to the client-requested field name
        yield [
            ['alias'],
            ['alias' => 'table.real_column'],
            ['qalias' => 'table.real_column'],
        ];

        // Nothing requested matches selectKeys: everything is dropped, select() gets an empty
        // array, which Yii's query builder treats as "SELECT *" - same as if this criterion had
        // never been applied at all, never as "select nothing"
        yield [
            ['ColumnC'],
            ['ColumnA', 'ColumnB'],
            [],
        ];
    }

    #[DataProvider('applyProvider')]
    #[AllowMockObjectsWithoutExpectations]
    public function testApplyQuery(array $fields, array $selectKeys, array $expectedSelect): void
    {
        $criteria = new SelectCriteria($this->mockConnection());
        $criteria->fields = $fields;
        $criteria->selectKeys = $selectKeys;

        $resultQuery = $criteria->apply(new db\Query);
        $this->assertEquals($expectedSelect, $resultQuery->select);
    }

    public static function validationProvider(): array
    {
        return [
            [[1,2,3], false,],
            [[], false,],
            [['col1', 'col2'], true,],
        ];
    }

    #[DataProvider('validationProvider')]
    public function testValidation(array $fields, bool $expectedResult): void
    {
        $connection = $this->createStub(db\Connection::class);
        $criteria = new SelectCriteria($connection);
        $criteria->fields = $fields;
        $this->assertEquals($expectedResult, $criteria->validate());
    }

    private function mockConnection(): db\Connection
    {
        // createPartialMock (not createStub) is required here: it overrides only the listed
        // methods and leaves yii\base\Component::__get() real, which is what makes the
        // $connection->schema magic property delegate to the overridden getSchema() below.
        // Not every case calls quoteSimpleColumnName (e.g. when nothing matches selectKeys),
        // so these are plain stubs rather than mock expectations.
        $schema = $this->createPartialMock(db\sqlite\Schema::class, ['quoteSimpleColumnName']);
        $schema
            ->method('quoteSimpleColumnName')
            ->willReturnCallback(fn(string $input) => "q{$input}");

        $connection = $this->createPartialMock(db\Connection::class, ['getSchema']);
        $connection
            ->method('getSchema')
            ->willReturn($schema);

        return $connection;
    }
}
