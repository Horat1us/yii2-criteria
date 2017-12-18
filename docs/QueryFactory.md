# Query Factory
Purpose of this factory is create method that will allow to easy merge criteria collection

## Example
```php
<?php

use yii\db\Query;
use yii\db\ActiveQuery;

use Horat1us\Yii\Criteria\Factories\QueryFactory;
use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;

use Horat1us\Yii\Criteria\SortCriteria;

/** @var ActiveQuery $query */

$factory = new QueryFactory($query);

// You can push class instance
$factory->push(new class implements CriteriaInterface {
   public function apply(Query $query): Query {
       return $query;
   } 
});

// Or just class name
$factory->push(SortCriteria::class);

// And configure classes using yii2-style
$factory->push([
    'class' => SortCriteria::class,
    'sortKeys' => ['id',],
]);

// Specify config to use Model::load 
$filteredQuery = $factory->apply([
    'SortCriteria' => [
        'fields' => [],
    ],
]);

print_r($filteredQuery === $query); // false. Passed query will not be modified

```
