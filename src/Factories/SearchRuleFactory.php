<?php

namespace Horat1us\Yii\Criteria\Factories;

use Horat1us\Yii\Criteria\Entities\SearchRuleEntity;

/**
 * Class SearchRuleFactory
 * @package Horat1us\Yii\Criteria\Factories
 */
class SearchRuleFactory
{
    /**
     * @param array|SearchRuleEntity $searchRule
     * @return SearchRuleEntity
     *
     * @throws \InvalidArgumentException
     */
    public function ensure($searchRule): SearchRuleEntity
    {
        if ($searchRule instanceof SearchRuleEntity) {
            return $searchRule;
        }

        if (!is_array($searchRule)) {
            throw new \InvalidArgumentException(
                'SearchRule have to be instance of ' . SearchRuleEntity::class . ' or to be an array'
            );
        }

        $searchRule = array_values($searchRule);
        if (count($searchRule) < 3) {
            throw new \InvalidArgumentException("Search rule must contain 3 elements", 0);
        }

        $searchOperator = $searchRule[0];
        if (!in_array($searchOperator, [
            SearchRuleEntity::OPERATOR_EQUALS,
            SearchRuleEntity::OPERATOR_SMALLER,
            SearchRuleEntity::OPERATOR_BIGGER,
            SearchRuleEntity::OPERATOR_SMALLER_OR_EQUALS,
            SearchRuleEntity::OPERATOR_BIGGER_OR_EMAI,
            SearchRuleEntity::OPERATOR_NOT_EQUAL,
            SearchRuleEntity::OPERATOR_LIKE,
            SearchRuleEntity::OPERATOR_IN,
        ])) {
            throw new \InvalidArgumentException("Search rule first element should be an operator", 1);
        }

        return new SearchRuleEntity($searchRule[1], $searchRule[2], $searchOperator);
    }
}