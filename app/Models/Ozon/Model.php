<?php

namespace App\Models\Ozon;

use App\Models\Ozon\ModelPrefix;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;
// use App\Models\Model;

class Model extends BaseModel
{
    use HasFactory/*, ModelPrefix*/;

    protected
    	$prefixTable = 'oz',
        $prefixSymbol = '_';

    public function __construct(array $attributes = []) {
        if (isset($this->prefixTable)) {
           $this->table = $this->prefixTable . $this->prefixSymbol . $this->getTable();
		}

       parent::__construct($attributes);
    }

}