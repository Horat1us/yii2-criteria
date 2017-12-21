<?php


namespace Horat1us\Yii\Criteria\Factories;

use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\base\Model;

use yii\db\Query;
use yii\di\Instance;

/**
 * Class QueryFactory
 * @package Horat1us\Yii\Criteria\Factories
 *
 * @property-read CriteriaInterface[]|array[]|string[] $criteria
 * @property-read Query $query
 */
class QueryFactory extends BaseObject
{
    /** @var Query */
    protected $query;

    /**
     * @var CriteriaInterface[]
     */
    protected $criteria;

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
            if ($criterion instanceof Model) {
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
     * @param Query $query
     */
    public function setQuery(Query $query)
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
            $this->criteria[] = Instance::ensure($criterion, CriteriaInterface::class);
        } catch (InvalidConfigException $exception) {
            throw new \InvalidArgumentException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $this;
    }
}