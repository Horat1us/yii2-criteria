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

// You can also use Yii2-way
$factory->push([
    'class' => SortCriteria::class,
    'sortKeys' => [
        'index', /* add string to allow sorting by key */
        'some.frontend.key' => 'your_database_key', /* sort key aliasing */    
    ],
]);

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


## Configuring project
You need to configure your DI container to give `yii\db\Connection` singleton instance
to constructor of SortCriteria 
```php
<?php

\Yii::$container->setSingleton(
    \yii\db\Connection::class,
    function() {
        // You may also add array definitions instead of using global object
        return \Yii::$app->db;
    }
);
```