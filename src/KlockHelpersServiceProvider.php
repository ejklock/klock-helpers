<?php

namespace KlockTecnologia\KlockHelpers;

use Illuminate\Support\ServiceProvider;

use KlockTecnologia\KlockHelpers\Console\BaseCommandsGeneratorsServiceProvider;

class KlockHelpersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'klock-helpers');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'klock-helpers');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('klock-helpers.php'),
            ], 'config');

            //$this->publishes()

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/klock-helpers'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/klock-helpers'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/klock-helpers'),
            ], 'lang');*/

            // Registering package commands.
            //$this->commands([DomainMakerCommand::class]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {

        $this->app->register(BaseCommandsGeneratorsServiceProvider::class);

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'klock-helpers');

        // Register the main class to use with the facade
        $this->app->singleton('klock-helpers', function () {
            return new KlockHelpers;
        });
    }
}
