<?php


namespace Horat1us\Yii\Criteria;


use Horat1us\Yii\Criteria\Entities\SearchRuleEntity;
use Horat1us\Yii\Criteria\Factories\SearchRuleFactory;
use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;
use yii\base\Model;
use yii\db\Query;
use yii\validators\SafeValidator;

/**
 * Class SearchCriteria
 * @package Horat1us\Yii\Criteria
 */
abstract class SearchCriteria extends Model implements CriteriaInterface
{
    /** @var array[] */
    public $query;

    /** @var SearchRuleFactory */
    protected $searchRuleFactory;

    public function __construct(SearchRuleFactory $searchRuleFactory, array $config = [])
    {
        parent::__construct($config);
        $this->searchRuleFactory = $searchRuleFactory;
    }

    public function rules()
    {
        return [
            ['query', 'required',],
            ['query', SafeValidator::class],
            ['query', 'each', 'rule' => ['each', 'rule' => 'safe',]],
        ];
    }

    public function apply(Query $query): Query
    {
        try {
            $rules = array_map(function ($plainSearchRule): SearchRuleEntity {
                return $this->searchRuleFactory->ensure($plainSearchRule);
            }, $this->query);
        } catch (\InvalidArgumentException $exception) {
            $this->addError('query', $exception->getMessage());
            return $query;
        }

        $searchKeys = $this->getSearchKeys();

        $rules = array_filter($rules, function (SearchRuleEntity $searchRule) use ($searchKeys) {
            $field = $searchRule->getField();
            if (array_key_exists($field, $searchKeys)) {
                $searchRule->setField($searchKeys[$field]);
                return true;
            }
            return in_array($field, $searchKeys);
        });

        /** @var SearchRuleEntity $rule */
        foreach ($rules as $rule) {
            $query->andWhere([$rule->getOperator(), $rule->getField(), $rule->getValue()]);
        }

        return $query;
    }

    /**
     * @return array
     */
    abstract protected function getSearchKeys(): array;
}