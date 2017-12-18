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
     * @param $searchRule
     * @return SearchRuleEntity
     *
     * @throws \InvalidArgumentException
     */
    public function ensure($searchRule): SearchRuleEntity
    {
        if ($searchRule instanceof SearchRuleEntity) {
            return $searchRule;
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
        ])) {
            throw new \InvalidArgumentException("Search rule first element should be an operator", 1);
        }

        return new SearchRuleEntity($searchOperator, $searchRule[1], $searchRule[2]);
    }
}