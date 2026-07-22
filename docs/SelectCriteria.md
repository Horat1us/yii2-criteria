# SelectCriteria
This criterion allows the client to choose which columns to select, restricted to a
server-defined whitelist.
To use you should extend [Horat1us\Yii\Criteria\SelectCriteria](../src/SelectCriteria.php)

Every requested field is resolved exclusively through `selectKeys` - a client-supplied
field is never trusted as a column/table name on its own. This is the same pattern
[SortCriteria](./SortCriteria.md) and [SearchCriteria](./SearchCriteria.md) already use for
`sortKeys`/`searchKeys`. Any field not present in `selectKeys` (as a key or a value) is silently
dropped.

## Usage
```php
<?php

use Horat1us\Yii\Criteria\Factories\QueryFactory;
use Horat1us\Yii\Criteria\SelectCriteria;

/** @var \yii\db\ActiveQuery $query */
$factory = new QueryFactory($query);

$factory->push(new class extends SelectCriteria {
    protected function getSelectKeys(): array {
        return [
            'index', /* add string to allow selecting this column as-is */
            'some.frontend.key' => 'your_table.your_database_key', /* select key aliasing/expression */
        ];
    }
});

// You can also use Yii2-way
$factory->push([
    'class' => SelectCriteria::class,
    'selectKeys' => [
        'index', /* add string to allow selecting this column as-is */
        'some.frontend.key' => 'your_table.your_database_key', /* select key aliasing/expression */
    ],
]);

$factory->apply(/* may be request from frontend */ [
    'SelectCriteria' => [
        'fields' => ['index', 'some.frontend.key'],
    ],
]);

```

## Note
A `selectKeys` value can be a full SQL expression (e.g. a cast, or a table-qualified column to
disambiguate a join), not just a bare column name - it's selected verbatim and aliased back to the
field name the client requested.

If none of the requested fields match `selectKeys`, `apply()` resolves to an empty select list.
Yii2 treats an empty `select()` as `SELECT *` - the same as if this criterion had never been
applied, never as "select nothing". Always set `selectKeys` (directly or via `getSelectKeys()`)
before using this criterion.
