<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as ModelBase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Model extends ModelBase {

	use HasFactory, HasCompositeKey;

	public static function findPrimary(...$ids) {
		// $modelClass = static::class;
        // $model = new $modelClass();
        $model = new static();

		$ids = array_combine($model->primaryKey, $ids);

		return static::find2($ids);
	}

    public static function upsertPrimary(array $values, $update = null) {
        // $modelClass = static::class;
        // $model = new $modelClass();
        $model = new static();
        // dump($model->primaryKey);
        return $model->upsert($values, $model->primaryKey, $update);
    }

	public static function scopeWherePrimary($q, ...$ids) {
		$q->whereKey($ids);
	}

    public static function find($id, $columns = ['*']) {
        // $modelClass = static::class;
        // $model = new $modelClass();
        $model = new static();

        $primaryKey = $model->primaryKey;
        $model->primaryKey = 'id';

        $model2 = $model->newQuery()
            ->find($id, $columns);

        $model->primaryKey = $primaryKey;

        return $model2;
    }

	public static function find2(array $ids)
    {
        $modelClass = static::class;
        $model = new $modelClass();
        $keys = $model->primaryKey;
        return $model->where(function($query) use($ids, $keys) {
            foreach ($keys as $key) {
            	$value = $ids[$key];
                if (isset($value)) {
                    $query->where($key, $value);
                } else {
                    $query->whereNull($key);
                }
            }
        })->first();
    }

}