# SearchCriteria
This criteria allow to implement simple search in database with any operator.  
To use you should extend [Horat1us/Yii/Criteria/SearchCriteria](../src/SearchCriteria.php)

## Operators
See [SearchRuleEntity](../src/Entities/SearchRuleEntity.php) constants.

## Usage
```php
<?php

use Horat1us\Yii\Criteria\Factories\QueryFactory;
use Horat1us\Yii\Criteria\SearchCriteria;

/** @var \yii\db\ActiveQuery $query */
$factory = new QueryFactory($query);

$factory->push(new class extends SearchCriteria {
    protected function getSearchKeys(): array {
        return [
            'index', /* add string to allow searching by key */
            'some.frontend.key' => 'your_database_key', /* search key aliasing */    
        ];
    } 
});

$factory->apply(/* may be request from frontend */ [
    'SearchCriteria' => [
        'query' => [
            ['=', 'index', 'some-index-value'],
            ['<>', 'some.frontend.key', 'some-value'],
        ]
    ],
]);

```