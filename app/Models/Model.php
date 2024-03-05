<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as ModelBase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Thiagoprz\CompositeKey\HasCompositeKey;
use Illuminate\Support\Facades\DB;

class Model extends ModelBase {

	use HasFactory, HasCompositeKey;

	public static function findPrimary(...$ids) {
        $model = new static();

		$ids = array_combine($model->primaryKey, $ids);

		return static::find2($ids);
	}

    public static function upsertPrimary(array $values, $update = null) {
        $model = new static();
        return $model->upsert($values, $model->primaryKey, $update);
    }

    public static function updateOrCreatePrimary() {
        
    }

	public static function scopeWherePrimary($q, ...$ids) {
		$q->whereKey($ids);
	}

    public static function find($id, $columns = ['*']) {
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

    public static function lastInsertId() {
        return DB::getPdo()->lastInsertId();
    }

    public function getKey()
    {
        $keys = $this->getKeyName();
        dump($keys, $this->getAttribute($keys));
        if (is_string($keys)) { dump($this);return $this->getAttribute($keys); }

        $values = [];
        array_map(function($key) use(&$values) {
            $values[] = $this->getAttribute($key);
        }, $keys);
        return $values;
    }

}