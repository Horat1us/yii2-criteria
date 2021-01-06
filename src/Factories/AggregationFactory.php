<?php


namespace Horat1us\Yii\Criteria\Factories;

use Horat1us\Yii\Criteria\Entities\AggregationEntity;

use yii\base\BaseObject;
use yii\db\Query;

/**
 * Class AggregationFactory
 * @package Horat1us\Yii\Criteria\Factories
 *
 * @property-read Query $query
 */
class AggregationFactory extends BaseObject
{
    /** @var Query */
    protected $query;

    /**
     * @param array $totalConfig
     * @see Query::select
     * @return AggregationEntity
     * @throws \yii\db\Exception
     */
    public function create(array $totalConfig = []): AggregationEntity
    {
        $query = clone $this->query;

        $query->limit = null;
        $query->offset = null;
        $query->orderBy = null;

        $row = $query
            ->select(array_merge($totalConfig, ['count' => 'count(*)',]))
            ->createCommand()
            ->queryOne(\PDO::FETCH_ASSOC);

        $count = $row['count'];
        unset($row['count']);

        return new AggregationEntity($count, $row);
    }

    /**
     * @param Query $query
     * @return AggregationFactory
     */
    public function setQuery(Query $query): AggregationFactory
    {
        $this->query = $query;
        return $this;
    }
}
