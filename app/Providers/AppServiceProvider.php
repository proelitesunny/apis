<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\UrlGenerator $url)
    {
        //generate secure url in production environment
        if(\App::environment('production') && config('services.prod_ssl_enabled'))
        {
          $url->forceSchema('https');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //register custom error logger
        // (new \App\MyHealthcare\Helpers\MyHealthLogger\ConfigureLogging())->bootstrap($this->app);
    }
}
