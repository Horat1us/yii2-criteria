# Yii2 Criteria

This package helps dealing with multiple search params.  


## Installing
Using [composer](http://getcomposer.org):  
```bash
composer require horat1us/yii2-criteria
```
*Requires PHP >= 7.0, yii2 >= 2.0.13*

## Usage

This code will find `SomeActiveRecord` with specified id (from URL).
```php
<?php

use YourApp\SomeActiveRecord;

use yii\db\Query;
use yii\base\Model;

use Horat1us\Yii\Criteria\Interfaces\CriteriaInterface;
use Horat1us\Yii\Criteria\Factories\QueryFactory;

$query = SomeActiveRecord::find();
$factory = new QueryFactory($query);

$factory->push(new class extends Model implements CriteriaInterface {
    
    public $id;
    
    public function rules()
    {
        return [['id', 'integer',]];
    }
    
    public function formName()
    {
        return 'IdCriteria';
    }
    
    public function apply(Query $query): Query {
        return $query->andWhere(['=', 'id', (int)$this->id]);
    }
    
});

$record = $factory->apply(\Yii::$app->request->queryParams)->one();
print_r($record->id, $_GET['IdCriteria']['id']); // true
```

## Pre-cooked criteria
- [SortCriteria](./docs/SortCriteria.md)
- [SearchCriteria](./docs/SearchCriteria.md)

## Contributors
[Alexander <horat1us> Letnikow](mailto:reclamme@gmail.com)

## License
[MIT](./LICENSE)

## Todo
- Tests
