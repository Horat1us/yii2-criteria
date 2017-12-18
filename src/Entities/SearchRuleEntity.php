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
        $this->operator = $operator;
        $this->value = $value;
        $this->field = $field;
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
}