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
    const OPERATOR_IN = 'in';

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
     * @param string|array $value
     * @param string $operator
     */
    public function __construct(string $field, $value, string $operator = self::OPERATOR_EQUALS)
    {
        $this
            ->setOperator($operator)
            ->setField($field)
            ->setValue($value);
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
        $this->setValue($this->value); // Value validation
        return $this;
    }

    /**
     * @param string|array $value
     * @return SearchRuleEntity
     */
    public function setValue($value): SearchRuleEntity
    {
        if ($this->operator === static::OPERATOR_IN && !is_array($value)) {
            throw new \InvalidArgumentException("Value have to be an array of operator is `in`");
        } elseif (!is_scalar($value)) {
            throw new \InvalidArgumentException("Value have to be scalar");
        }

        $this->value = $value;
        return $this;
    }
}