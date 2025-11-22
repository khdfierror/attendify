<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {

        Validator::extend('LivewireMaxFileName', function ($attribute, $value, $parameters, $validator) {
            return strlen($value->getClientOriginalName()) <= 100;
        }, 'Nama file terlalu panjang, pastikan nama file tidak lebih dari 100 karakter.');
        
    }
}
