<?php


namespace Horat1us\Yii\Criteria\Interfaces;

use yii\db\Query;

interface CriteriaInterface
{
    public function apply(Query $query): Query;
}