<?php


namespace Horat1us\Yii\Criteria;


use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;
use yii\base\Model;
use yii\data\Pagination;
use yii\db\Query;

/**
 * Class PaginationCriteria
 * @package Horat1us\Yii\Criteria
 */
class PaginationCriteria extends Model implements CriteriaInterface
{
    /** @var int */
    public $pageSize;

    /** @var int */
    public $page;

    /** @var int[] */
    public $allowedPageSize;

    public function rules()
    {
        $rules = [
            [['page', 'pageSize',], 'required',],
            [['page',], 'integer', 'min' => 0,],
            [['pageSize',], 'integer', 'min' => 1,],
        ];

        if (is_array($this->allowedPageSize) || is_callable($this->allowedPageSize)) {
            $rules[] = [['pageSize'], 'in', 'range' => $this->allowedPageSize];
        }

        return $rules;
    }

    public function apply(Query $query): Query
    {
        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'page' => $this->page,
            'pageSize' => $this->pageSize,
        ]);

        return $query
            ->offset($pagination->offset)
            ->limit($pagination->limit);
    }
}