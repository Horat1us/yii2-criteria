<?php declare(strict_types=1);

namespace Horat1us\Yii\Criteria;

use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;
use yii\base\Model;
use yii\data\Pagination;
use yii\db\Query;

class PaginationCriteria extends Model implements CriteriaInterface
{
    public ?int $pageSize = null;

    public ?int $page = null;

    /** @var int[]|\Closure|\Traversable */
    public $allowedPageSize;

    public function rules(): array
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
            'pageSize' => $this->pageSize,
            'validatePage' => true,
        ]);
        $pagination->setPage($this->page, true);

        return $query
            ->offset($pagination->offset)
            ->limit($pagination->limit);
    }
}
