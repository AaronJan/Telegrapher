<?php

namespace Telegrapher;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Telegrapher\Contracts\Signer;
use Telegrapher\Contracts\Authenticator;
use Telegrapher\Contracts\Stringifier;
use Telegrapher\Processors\HMACSigner;
use Telegrapher\Processors\JSONStringifier;

class Provider extends ServiceProvider
{
    const DEFAULT_CONFIG_PATH = __DIR__ . '/../../config/telegrapher.php';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            static::DEFAULT_CONFIG_PATH => config_path('telegrapher.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(static::DEFAULT_CONFIG_PATH, 'telegrapher');

        $this->registerAuthenticator();

        $this->app->singleton(Stringifier::class, function (Application $application) {
            return new JSONStringifier();
        });

        $this->app->singleton(Signer::class, function (Application $application) {
            return new HMACSigner();
        });

        $this->app->singleton(
            \Telegrapher\Contracts\Telegrapher::class,
            Telegrapher::class
        );
    }

    /**
     *
     */
    protected function registerAuthenticator()
    {
        $customAuthenticator = config('telegrapher.authenticator');
        $authentication      = config('telegrapher.authentication');

        $this->app->singleton(Authenticator::class, function (Application $application) use (
            $customAuthenticator,
            $authentication
        ) {
            if ($customAuthenticator) {
                return new $customAuthenticator($authentication);
            } else {
                return new TimeAuthenticator($authentication);
            }
        });
    }
}
