<?php


namespace Horat1us\Yii\Criteria\Entities;


/**
 * Class SortEntity
 * @package Horat1us\Yii\Criteria\Entities
 */
class SortEntity
{
    /** @var string */
    protected $field;

    /** @var bool */
    protected $desc;

    /**
     * SortEntity constructor.
     * @param string $field
     * @param bool $desc
     */
    public function __construct(string $field, bool $desc = false)
    {
        $this
            ->setField($field)
            ->setDesc($desc);
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return bool
     */
    public function isDesc(): bool
    {
        return $this->desc;
    }

    /**
     * @param string $field
     * @return SortEntity
     */
    public function setField(string $field): SortEntity
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @param bool $desc
     * @return SortEntity
     */
    public function setDesc(bool $desc = false): SortEntity
    {
        $this->desc = $desc;
        return $this;
    }
}