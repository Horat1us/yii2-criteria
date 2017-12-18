<?php


namespace Horat1us\Yii\Criteria\Interfaces;

use yii\db\Query;

/**
 * Interface CriteriaInterface
 * @package Horat1us\Yii\Criteria\Interfaces
 */
interface CriteriaInterface
{
    public function apply(Query $query): Query;
}