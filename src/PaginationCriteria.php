<?php declare(strict_types=1);

namespace Horat1us\Yii\Criteria;

use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;
use yii\base\Model;
use yii\data\Pagination;
use yii\db\Query;

/**
 * Invalid client input falls back to the page and page size configured at construction:
 * a criterion failing validation is skipped by QueryFactory, which would return every row.
 */
class PaginationCriteria extends Model implements CriteriaInterface
{
    /** Raw client input until validated, int afterwards */
    public mixed $pageSize = null;

    /** Raw client input until validated, int afterwards */
    public mixed $page = null;

    /** @var int[]|\Closure|\Traversable */
    public $allowedPageSize;

    public ?int $maxPageSize = null;

    private mixed $defaultPageSize = null;

    private mixed $defaultPage = null;

    public function init(): void
    {
        parent::init();
        $this->defaultPageSize = $this->pageSize;
        $this->defaultPage = $this->page;
    }

    public function rules(): array
    {
        $rules = [
            [['page', 'pageSize',], 'required',],
            [['page',], 'integer', 'min' => 0,],
            [['pageSize',], 'integer', 'min' => 1, 'max' => $this->maxPageSize,],
        ];

        if (is_array($this->allowedPageSize) || is_callable($this->allowedPageSize)) {
            $rules[] = [['pageSize'], 'in', 'range' => $this->allowedPageSize];
        }

        // Skipped for attributes that already failed validation
        $rules[] = [['page', 'pageSize',], 'filter', 'filter' => 'intval',];

        return $rules;
    }

    public function afterValidate(): void
    {
        $failed = $this->hasErrors('page') || $this->hasErrors('pageSize');
        if ($failed && !is_null($this->defaultPage) && !is_null($this->defaultPageSize)) {
            $this->page = $this->defaultPage;
            $this->pageSize = $this->defaultPageSize;
            $this->clearErrors('page');
            $this->clearErrors('pageSize');
        }

        parent::afterValidate();
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
