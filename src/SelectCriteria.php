<?php

namespace Horat1us\Yii\Criteria;

use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Query;

/**
 * Class SelectCriteria
 * @package Horat1us\Yii\Criteria
 */
class SelectCriteria extends Model implements CriteriaInterface
{
    /** @var string[] */
    public $fields;

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
            ['fields', 'each', 'rule' => ['string',]],
        ];
    }

    public function apply(Query $query): Query
    {
        $fields = $this->fields;

        if ($query instanceof ActiveQuery) {
            /** @var ActiveRecord $record */
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

        return $query->select(array_map(
            function (string $field) {
                return $this->connection->schema->quoteSimpleColumnName($field);
            },
            $fields
        ));
    }
}
