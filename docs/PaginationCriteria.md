# PaginationCriteria
This class allows to paginate your query results

## Example
```php
<?php

use Horat1us\Yii\Criteria\Factories\QueryFactory;
use Horat1us\Yii\Criteria\PaginationCriteria;

/** @var \yii\db\Query $query */
$factory = new QueryFactory(['query' => $query,]);

/* page and pageSize configured here are the defaults, see below */
$factory->push([
    'class' => PaginationCriteria::class,
    'page' => 0,
    'pageSize' => 10,
]);

/* if you want to constrain pageSize */
$factory->push([
    'class' => PaginationCriteria::class,
    'page' => 0,
    'pageSize' => 10,
    'maxPageSize' => 100,
]);
/* or allow only specific values */
$factory->push([
    'class' => PaginationCriteria::class,
    'page' => 0,
    'pageSize' => 10,
    'allowedPageSize' => [5, 10, 25],
]);
/* you can also pass Closure */
$factory->push([
    'class' => PaginationCriteria::class,
    'page' => 0,
    'pageSize' => 10,
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

## Defaults and invalid input
`page` and `pageSize` configured at construction (config array or `Yii::$container` definition)
are used when the request has no pagination. They also replace both values when the request has
invalid ones: non-integer, negative `page`, `pageSize` below 1, above `maxPageSize` or not in
`allowedPageSize`. So `pageSize: 3` with the allow-list above returns the first page of 10 rows.

Without configured `page` and `pageSize` there is nothing to fall back to: invalid input fails
validation and [QueryFactory](./QueryFactory.md) skips the criterion, so the query is not paginated.

After a successful validation `page` and `pageSize` are integers.
