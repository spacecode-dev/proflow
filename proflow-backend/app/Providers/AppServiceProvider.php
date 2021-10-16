<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Auth;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('currentpassword', function($attribute, $value, $parameters) {
        $hasher = app('hash');
        if ($hasher->check($value, Auth::user()->password)) {
        return true;
        }
        return false;
        });
    }
}
