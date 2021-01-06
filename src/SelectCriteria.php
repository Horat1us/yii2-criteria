<?php declare(strict_types=1);

namespace Horat1us\Yii\Criteria;

use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;
use yii\base;
use yii\db;

class SelectCriteria extends base\Model implements CriteriaInterface
{
    /** @var string[] */
    public ?array $fields = null;

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
        $fields = $this->fields;

        if ($query instanceof db\ActiveQuery) {
            /** @var db\ActiveRecord $record */
            $record = new $query->modelClass;
            $attributes = $record->attributes();
            $fields = array_filter(
                $fields,
                function (string $field) use ($attributes) {
                    return in_array($field, $attributes)
                        || in_array(explode(".", $field, 2)[1] ?? $field, $attributes);
                }
            );
        }

        $schema = $this->connection->schema;
        return $query->select(array_map(
            fn(string $field): string => $schema->quoteSimpleColumnName($field),
            $fields
        ));
    }
}
