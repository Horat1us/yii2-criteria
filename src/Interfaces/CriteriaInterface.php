<?php declare(strict_types=1);

namespace Horat1us\Yii\Criteria\Interfaces;

use yii\db;

interface CriteriaInterface
{
    public function apply(db\Query $query): db\Query;
}
