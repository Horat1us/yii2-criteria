# SortCriteria
This criterion allows to implement easy ordering any query.  
To use you should extend [Horat1us\Yii\Criteria\SortCriteria](../src/SortCriteria.php)

## Usage
```php
<?php

use Horat1us\Yii\Criteria\Factories\QueryFactory;
use Horat1us\Yii\Criteria\SortCriteria;

/** @var \yii\db\ActiveQuery $query */
$factory = new QueryFactory($query);

$factory->push(new class extends SortCriteria {
    protected function getSortKeys(): array {
        return [
            'index', /* add string to allow sorting by key */
            'some.frontend.key' => 'your_database_key', /* sort key aliasing */    
        ];
    } 
});

$factory->apply(/* may be request from frontend */ [
    'SortCriteria' => [
        'fields' => [
            'index', /* string to asc sorting by key */
            ['id' => 'index',], /* or this way to asc sorting */
            ['id' => 'some.frontend.key', 'desc' => true,], /* add desc key for desc sorting */
        ]
    ],
]);

```
