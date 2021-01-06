<?php declare(strict_types=1);

namespace Horat1us\Yii\Criteria\Tests;

use Horat1us\Yii\Criteria\PaginationCriteria;
use PHPUnit\Framework\TestCase;

class PaginationCriteriaTest extends TestCase
{
    public function validationProvider(): array
    {
        return [
            [new PaginationCriteria(), null, false,],
            [new PaginationCriteria(['page' => 0, 'pageSize' => 1]), null, true,],
            [new PaginationCriteria(['page' => -1,]), ['page'], false,],
            [new PaginationCriteria(['pageSize' => 0,]), ['pageSize'], false,],
            [new PaginationCriteria(['pageSize' => 10, 'allowedPageSize' => fn() => [20, 10,]]), ['pageSize'], true],
            [new PaginationCriteria(['pageSize' => 10, 'allowedPageSize' => [1, 2]]), ['pageSize'], false],
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidation(PaginationCriteria $criteria, ?array $attributes, bool $expectedResult): void
    {
        $this->assertEquals($criteria->validate($attributes), $expectedResult);
    }
}