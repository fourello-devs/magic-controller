<?php

namespace FourelloDevs\MagicController;

use FourelloDevs\MagicController\Console\Commands\ExtendedMakeController;
use FourelloDevs\MagicController\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class MagicControllerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'fourello-devs');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'fourello-devs');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        // Register Helpers
        $this->registerHelpers();

        // Register Custom Exception Handler
        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/magic-controller.php', 'magic-controller');

        // Register the service the package provides.
//        $this->app->singleton('magic-controller', function ($app) {
//            return new MagicController;
//        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
//        return ['magic-controller'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/magic-controller.php' => config_path('magic-controller.php'),
        ], 'magic-controller.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/fourello-devs'),
        ], 'magic-controller.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/fourello-devs'),
        ], 'magic-controller.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/fourello-devs'),
        ], 'magic-controller.views');*/

        // Registering package commands.
        $this->commands([
            ExtendedMakeController::class,
        ]);
    }

    /**
     * Register helpers file
     */
    public function registerHelpers(): void
    {
        Log::info('Registering...', ['file_exists' => File::exists(__DIR__ . '/../helpers/CustomHelpers.php')]);
        if (! function_exists('customResponse') && File::exists(__DIR__ . '/../helpers/CustomHelpers.php')) {
            Log::info('Yes, it exists!');
            require_once __DIR__ . '/../helpers/CustomHelpers.php';
        }
        Log::info('Done registering...', [function_exists('customResponse')]);
    }
}
