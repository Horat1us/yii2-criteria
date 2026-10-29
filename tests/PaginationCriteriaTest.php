<?php declare(strict_types=1);

namespace Horat1us\Yii\Criteria\Tests;

use Horat1us\Yii\Criteria\Factories\QueryFactory;
use Horat1us\Yii\Criteria\PaginationCriteria;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use yii\db;

class PaginationCriteriaTest extends TestCase
{
    public static function validationProvider(): array
    {
        return [
            [new PaginationCriteria(), null, false,],
            [new PaginationCriteria(['page' => 0, 'pageSize' => 1]), null, true,],
            [new PaginationCriteria(['page' => -1,]), ['page'], false,],
            [new PaginationCriteria(['pageSize' => 0,]), ['pageSize'], false,],
            [new PaginationCriteria(['pageSize' => 10, 'allowedPageSize' => fn() => [20, 10,]]), ['pageSize'], true],
            [new PaginationCriteria(['pageSize' => 10, 'allowedPageSize' => [1, 2]]), ['pageSize'], false],
            [new PaginationCriteria(['pageSize' => 100, 'maxPageSize' => 100]), ['pageSize'], true],
            [new PaginationCriteria(['pageSize' => 101, 'maxPageSize' => 100]), ['pageSize'], false],
        ];
    }

    #[DataProvider('validationProvider')]
    public function testValidation(PaginationCriteria $criteria, ?array $attributes, bool $expectedResult): void
    {
        $this->assertEquals($criteria->validate($attributes), $expectedResult);
    }

    public static function fallbackProvider(): iterable
    {
        $configured = ['page' => 0, 'pageSize' => 20, 'maxPageSize' => 100,];

        yield 'non-numeric page' => [$configured, ['page' => 'x', 'pageSize' => '50',],];
        yield 'negative page' => [$configured, ['page' => '-1',],];
        yield 'array page' => [$configured, ['page' => [1,],],];
        yield 'zero page size' => [$configured, ['pageSize' => '0',],];
        yield 'non-numeric page size' => [$configured, ['pageSize' => 'all',],];
        yield 'fractional page size' => [$configured, ['pageSize' => '2.5',],];
        yield 'array page size' => [$configured, ['pageSize' => [5,],],];
        yield 'page size above max' => [$configured, ['page' => '3', 'pageSize' => '1000',],];
        yield 'page size not allowed' => [
            ['page' => 0, 'pageSize' => 20, 'allowedPageSize' => [10, 20, 50,],],
            ['page' => '3', 'pageSize' => '3',],
        ];
    }

    #[DataProvider('fallbackProvider')]
    public function testFallback(array $config, array $input): void
    {
        $criteria = new PaginationCriteria($config);
        $criteria->load(['PaginationCriteria' => $input,]);

        $this->assertTrue($criteria->validate());
        $this->assertSame(0, $criteria->page);
        $this->assertSame(20, $criteria->pageSize);
    }

    public function testValidInputIsNormalized(): void
    {
        $criteria = new PaginationCriteria(['page' => 0, 'pageSize' => 20, 'maxPageSize' => 100,]);
        $criteria->load(['PaginationCriteria' => ['page' => '2', 'pageSize' => '50',],]);

        $this->assertTrue($criteria->validate());
        $this->assertSame(2, $criteria->page);
        $this->assertSame(50, $criteria->pageSize);
    }

    public function testInvalidInputWithoutConfiguredDefaultsFails(): void
    {
        $criteria = new PaginationCriteria(['page' => 0,]);
        $criteria->load(['PaginationCriteria' => ['page' => 'x', 'pageSize' => '10',],]);

        $this->assertFalse($criteria->validate());
        $this->assertTrue($criteria->hasErrors('page'));
    }

    public static function queryFactoryProvider(): iterable
    {
        yield 'valid' => [['page' => '2', 'pageSize' => '10',], 20, 10,];
        yield 'invalid' => [['page' => '2', 'pageSize' => '1000',], 0, 20,];
        yield 'missing' => [[], 0, 20,];
    }

    #[DataProvider('queryFactoryProvider')]
    public function testQueryFactory(array $input, int $expectedOffset, int $expectedLimit): void
    {
        $query = new class extends db\Query {
            public function count($q = '*', $db = null)
            {
                return 100;
            }
        };
        $factory = new QueryFactory([
            'query' => $query,
            'criteria' => [
                ['class' => PaginationCriteria::class, 'page' => 0, 'pageSize' => 20, 'maxPageSize' => 100,],
            ],
        ]);

        $result = $factory->apply(['PaginationCriteria' => $input,]);

        $this->assertSame($expectedOffset, $result->offset);
        $this->assertSame($expectedLimit, $result->limit);
    }
}
