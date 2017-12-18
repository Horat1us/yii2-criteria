# PaginationCriteria
This class allows to paginate your query results

## Example
```php
<?php

use Horat1us\Yii\Criteria\Factories\QueryFactory;
use Horat1us\Yii\Criteria\PaginationCriteria;

/** @var \yii\db\Query $query */
$factory = new QueryFactory($query);

$factory->push(PaginationCriteria::class);

/* if you want to constrain pageSize */
$factory->push([
    'class' => PaginationCriteria::class,
    'allowedPageSize' => [5, 10, 25],
]);
/* you can also pass Closure */
$factory->push([
    'class' => PaginationCriteria::class,
    'allowedPageSize' => function() {
        return [5, 10, 25];
    },
]);

$factory->apply(/* may be your frontend request */ [
    'PaginationCriteria' => [
        'page' => 1,
        'pageSize' => 5,
    ],
]);

```