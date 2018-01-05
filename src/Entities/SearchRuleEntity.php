<?php


namespace Horat1us\Yii\Criteria\Entities;


/**
 * Class SearchRuleEntity
 * @package Horat1us\Yii\Criteria\Entities
 */
class SearchRuleEntity
{
    const OPERATOR_EQUALS = '=';
    const OPERATOR_SMALLER = '<';
    const OPERATOR_BIGGER = '>';
    const OPERATOR_SMALLER_OR_EQUALS = '<=';
    const OPERATOR_BIGGER_OR_EMAI = '>=';
    const OPERATOR_NOT_EQUAL = '<>';
    const OPERATOR_LIKE = 'like';

    /** @var string */
    protected $operator;

    /** @var string */
    protected $field;

    /** @var string */
    protected $value;

    /**
     * SearchRuleEntity constructor.
     *
     * @param string $field
     * @param string $value
     * @param string $operator
     */
    public function __construct(string $field, string $value, string $operator = self::OPERATOR_EQUALS)
    {
        $this
            ->setField($field)
            ->setValue($value)
            ->setOperator($operator);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $field
     * @return SearchRuleEntity
     */
    public function setField(string $field): SearchRuleEntity
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @param string $operator
     * @return SearchRuleEntity
     */
    public function setOperator(string $operator = self::OPERATOR_EQUALS): SearchRuleEntity
    {
        $this->operator = $operator;
        return $this;
    }

    /**
     * @param string $value
     * @return SearchRuleEntity
     */
    public function setValue(string $value): SearchRuleEntity
    {
        $this->value = $value;
        return $this;
    }
}