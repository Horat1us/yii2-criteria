<?php declare(strict_types=1);

namespace Horat1us\Yii\Criteria;

use Horat1us\Yii\Criteria\Entities\SearchRuleEntity;
use Horat1us\Yii\Criteria\Factories\SearchRuleFactory;
use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;
use yii\base;
use yii\db;

class SearchCriteria extends base\Model implements CriteriaInterface
{
    public array $query = [];

    /**
     * @var string[]
     * @see getSearchKeys()
     */
    public array $searchKeys = [];

    protected SearchRuleFactory $searchRuleFactory;

    protected db\Connection $connection;

    public function __construct(
        db\Connection $connection,
        SearchRuleFactory $searchRuleFactory,
        array $config = []
    ) {
        parent::__construct($config);

        $this->searchRuleFactory = $searchRuleFactory;
        $this->connection = $connection;
    }

    public function rules()
    {
        return [
            ['query', 'required',],
            ['query', 'safe'],
        ];
    }

    public function apply(db\Query $query): db\Query
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
            $query->andWhere([
                $rule->getOperator(),
                $this->connection->schema->quoteSimpleColumnName($rule->getField()),
                $rule->getValue(),
            ]);
        }

        return $query;
    }

    /**
     * @return string[] with value as search key, or key as search key alias and value as real search key
     */
    protected function getSearchKeys(): array
    {
        return $this->searchKeys;
    }
}
