<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;

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

    public function boot()
    {
        app('validator')->extend('phone', function ($attribute, $value) {
            return preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i', $value) && strlen($value) >= 10;
        });

        app('validator')->extend('name', function ($attribute, $value) {
            return preg_match('/^[a-zA-Z \'.-]*$/', $value);
        });

        app('validator')->extend('account_type', function($attribute, $value){
            return in_array(strtolower($value),[User::BORROWER, User::LENDER]);
        });

        app('validator')->replacer('account_type', function ($message, $attribute, $rule, $parameters) {
            return 'Selected account type invalid. Choose (borrower or lender)';
        });

    }
}
