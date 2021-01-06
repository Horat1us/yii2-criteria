<?php

namespace Horat1us\Yii\Criteria\Factories;

use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;

use yii\base;
use yii\db;
use yii\di;

/**
 * Class QueryFactory
 * @package Horat1us\Yii\Criteria\Factories
 *
 * @property-read CriteriaInterface[]|array[]|string[] $criteria
 * @property-read db\Query $query
 */
class QueryFactory extends base\BaseObject
{
    /** @var db\Query */
    protected db\Query $query;

    /**
     * @var CriteriaInterface[]
     */
    protected array $criteria = [];

    /**
     * @param array $params
     * @return db\Query
     * @throws \yii\base\InvalidConfigException
     */
    public function apply(array $params): db\Query
    {
        $query = clone $this->query;
        foreach ($this->criteria as $criterion) {
            /** @var CriteriaInterface $criterion */
            $criterion = di\Instance::ensure($criterion, CriteriaInterface::class);
            if ($criterion instanceof base\Model) {
                $criterion->load($params);
                if (!$criterion->validate()) {
                    continue;
                }
            }
            $criterion->apply($query);
        }
        return $query;
    }

    /**
     * @param db\Query $query
     */
    public function setQuery(db\Query $query)
    {
        $this->query = $query;
    }

    /**
     * @param CriteriaInterface[]|array[]|string[] $criteria
     * @see Instance::ensure()
     */
    public function setCriteria(array $criteria = [])
    {
        foreach ($criteria as $criterion) {
            $this->push($criterion);
        }
    }

    /**
     * @param string|array|CriteriaInterface $criterion
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function push($criterion): self
    {
        try {
            $this->criteria[] = di\Instance::ensure($criterion, CriteriaInterface::class);
        } catch (base\InvalidConfigException $exception) {
            throw new \InvalidArgumentException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $this;
    }
}
