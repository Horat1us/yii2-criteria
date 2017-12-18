# Aggregation Factory
This factory creates row with aggregation result for specified query
without applying pagination and modifying passed query.

## Example

```php
<?php

use Horat1us\Yii\Criteria\Factories\AggregationFactory;

/** @var \yii\db\ActiveQuery $query */
$factory = new AggregationFactory($query);
$aggregationRow = $factory->create([
    'sum' => 'sum(payment.amount)' // Your custom aggregation query
]);

echo $aggregationRow->getCount(); // Total count
echo $aggregationRow->getTotal()['sum']; // Your custom value

```