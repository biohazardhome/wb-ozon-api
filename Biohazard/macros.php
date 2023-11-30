<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

Collection::macro('keysToSnake', function(array $keysRename = []) {
    return $this->map(function($item) use($keysRename) {

        $arr = [];
        foreach($item as $property => $value) {
            if (isset($keysRename[$property])) {
                $property = str_replace($property, $keysRename[$property], $property); // чтобы небыло i_d_ или s_c_c_ode
            }
            
            $property = Str::snake($property);
            $arr[$property] = $value;
        }

        return $arr;
    });
});

function first(...$args) {
	return Arr::first(...$args);
    // return reset($args[0]);
}