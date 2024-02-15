<?php

namespace Kuliks08\TwoFA;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;

class TwoFAServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {

        $app = $this->app;

        $app['validator']->extend('voyager_2fa', function ($attribute, $value) use ($app) {
            return $app['voyager_2fa']->verifyResponse($value, $app['request']->getClientIp());
        });

        if ($app->bound('form')) {
            $app['form']->macro('voyager_recaptcha', function ($attributes = []) use ($app) {
                return $app['voyager_recaptcha']->display($attributes, $app->getLocale());
            });
        }

        $this->publishes([
            // Views
            __DIR__ . '/../resources/views/vendor/voyager/login.blade.php' => resource_path('views/vendor/voyager/login.blade.php'),
            // Configs
            __DIR__ . '/config/voyager-2fa.php' => config_path('voyager-2fa.php'),
        ], 'voyager-2fa');

        // Загрузка переводов из директории ресурсов
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', '2fa');

        // Публикация миграций
        $this->publishes([
            __DIR__ . '/../migrations/' => database_path('migrations')
        ], '2fa-migrations');

        // Регистрация переводов
        Lang::addNamespace('2fa', __DIR__ . '/../resources/lang');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(Commands\InstallCommand::class);
        }

        $this->app->singleton('voyager_2fa', function ($app) {
            return new TwoFA(
                $app['config']['voyager-2fa.timeout']
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['voyager_2fa'];
    }
}