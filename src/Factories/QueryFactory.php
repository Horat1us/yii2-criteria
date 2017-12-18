<?php


namespace Horat1us\Yii\Criteria\Factories;

use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;

use yii\base\Model;
use yii\db\Query;
use yii\di\Instance;

/**
 * Class QueryFactory
 * @package Horat1us\Yii\Criteria\Factories
 */
class QueryFactory
{
    /** @var Query */
    protected $query;

    /**
     * @var string[]|CriteriaInterface[]
     */
    protected $criteria;

    /**
     * QueryFactory constructor.
     * @param Query $query
     * @param array $criteria
     */
    public function __construct(Query $query, array $criteria = [])
    {
        $this->query = $query;
        $this->criteria = $criteria;
    }

    /**
     * @param array $params
     * @return Query
     * @throws \yii\base\InvalidConfigException
     */
    public function apply(array $params): Query
    {
        $query = clone $this->query;
        foreach ($this->criteria as $criterion) {
            /** @var CriteriaInterface $criterion */
            $criterion = Instance::ensure($criterion, CriteriaInterface::class);
            if ($criterion instanceof Model && (!$criterion->load($params) || !$criterion->validate())) {
                continue;
            }
            $criterion->apply($query);
        }
        return $query;
    }

    /**
     * @param Query $query
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @param string $criteria
     * @return $this
     */
    public function push(string $criteria): self
    {
        $this->criteria[] = $criteria;
        return $this;
    }
}