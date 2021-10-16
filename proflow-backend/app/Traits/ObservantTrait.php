<?php

namespace App\Traits;

use App\Observers\AppModelObserver;


trait ObservantTrait
{
    public static function bootObservantTrait()
    {
        
        static::observe(new AppModelObserver);
    }
}
