<?php

namespace Telegrapher\Authentication;

use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Telegrapher\Contracts\Telegrapher;
use Telegrapher\Authentication\Contracts\Identifier as IdentifierContract;

class Provider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend('telegrapher', function (Application $application, $name, array $config) {
            return new Guard(
                Auth::createUserProvider($config['provider']),
                $this->app['request'],
                $application->make(Telegrapher::class),
                $application->make(\Telegrapher\Authentication\Contracts\Identifier::class)
            );
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(IdentifierContract::class, function (Application $application) {
            return new Identifier($this->app['request']);
        });
    }

}
