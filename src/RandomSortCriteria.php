<?php

namespace Horat1us\Yii\Criteria;

use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;
use yii\base\Model;
use yii\db\Query;

class RandomSortCriteria extends Model implements CriteriaInterface
{
    /** @var bool */
    public $trigger;

    public function rules()
    {
        return [
            ['trigger', 'required',],
            ['trigger', 'boolean',],
        ];
    }

    public function apply(Query $query): Query
    {
        return $query->orderBy("random()");
    }
}
