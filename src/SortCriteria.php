<?php


namespace Horat1us\Yii\Criteria;


use Horat1us\Yii\Criteria\Entities\SortEntity;
use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;
use yii\base\Model;
use yii\db\Connection;
use yii\db\Query;
use yii\validators\EachValidator;

/**
 * Class SortCriteria
 * @package Horat1us\Yii\Criteria
 */
class SortCriteria extends Model implements CriteriaInterface
{
    /** @var string|array|string[]|array[] */
    public $fields;

    /**
     * @var string[]
     * @see getSortKeys()
     */
    public $sortKeys;

    /** @var Connection */
    protected $connection;

    public function __construct(Connection $connection, array $config = [])
    {
        parent::__construct($config);
        $this->connection = $connection;
    }

    public function rules()
    {
        return [
            ['fields', 'required',],
            ['fields', EachValidator::class, 'rule' => [function ($field) {
                if (is_string($field) || (is_array($field) && array_key_exists('id', $field))) {
                    return;
                }
                $this->addError('fields', 'One of fields is invalid');
            }]],
        ];
    }

    public function formName()
    {
        return 'SortCriteria';
    }

    public function apply(Query $query): Query
    {
        /** @var SortEntity[] $fields */
        $fields = array_map(function ($field): SortEntity {
            return is_string($field)
                ? new SortEntity($field)
                : new SortEntity($field['id'], array_key_exists('desc', $field) && $field['desc']);
        }, (array)$this->fields);

        $sortKeys = $this->getSortKeys();

        // todo: optimization, too complex
        $fields = array_filter($fields, function (SortEntity $sortEntity) use ($sortKeys) {
            $field = $sortEntity->getField();
            if (array_key_exists($field, $sortKeys)) {
                $sortEntity->setField($sortKeys[$field]);
                return true;
            }
            return in_array($field, $sortKeys);
        });

        $order = [];
        foreach ($fields as $field) {
            /* using brackets to prevent quoting field names that contains dot */
            $order[$this->connection->schema->quoteSimpleColumnName($field->getField())] = $field->isDesc()
                ? SORT_DESC
                : SORT_ASC;
        }

        return $query->orderBy($order);
    }

    /**
     * Returns list of allowed keys for sorting
     * Also allows aliases for sorting.
     *
     * @return string[] with value as sort key, or key as sort key alias and value as real sort key
     */
    protected function getSortKeys(): array
    {
        return $this->sortKeys;
    }
}
