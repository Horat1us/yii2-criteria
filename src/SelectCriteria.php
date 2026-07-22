<?php declare(strict_types=1);

namespace Horat1us\Yii\Criteria;

use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;
use yii\base;
use yii\db;

class SelectCriteria extends base\Model implements CriteriaInterface
{
    /** @var string[] */
    public ?array $fields = null;

    /**
     * @var array<int|string, string> with value as a selectable column/expression, or key as the
     *      field name the client may request and value as the real column/expression to select for it
     * @see getSelectKeys()
     */
    public array $selectKeys = [];

    protected db\Connection $connection;

    public function __construct(db\Connection $connection, array $config = [])
    {
        parent::__construct($config);
        $this->connection = $connection;
    }

    public function rules(): array
    {
        return [
            ['fields', 'required',],
            ['fields', 'each', 'rule' => ['string',]],
        ];
    }

    public function apply(db\Query $query): db\Query
    {
        $selectKeys = $this->getSelectKeys();

        $resolved = [];
        foreach ($this->fields as $field) {
            if (array_key_exists($field, $selectKeys)) {
                $expression = $selectKeys[$field];
                $alias = $this->connection->schema->quoteSimpleColumnName($field);
                $resolved[] = "{$expression} AS {$alias}";
            } elseif (in_array($field, $selectKeys, true)) {
                $resolved[] = $this->connection->schema->quoteSimpleColumnName($field);
            }
        }

        return $query->select($resolved);
    }

    /**
     * @return array<int|string, string> with value as a selectable column/expression, or key as a
     *         client-facing field alias and value as the real column/expression to select for it
     */
    protected function getSelectKeys(): array
    {
        return $this->selectKeys;
    }
}
