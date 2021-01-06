<?php


namespace Horat1us\Yii\Criteria\Entities;

/**
 * Class AggregationEntity
 * @package Horat1us\Yii\Criteria\Entities
 */
class AggregationEntity
{
    /** @var number[] */
    protected $total;

    /** @var int */
    protected $count;

    public function __construct(int $count, array $total = [])
    {
        $this->count = $count;
        $this->total = $total;
    }

    public function getTotal(): array
    {
        return $this->total;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
