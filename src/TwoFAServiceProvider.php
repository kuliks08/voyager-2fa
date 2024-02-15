<?php

namespace Kuliks08\TwoFA;

use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class TwoFAServiceProvider extends ServiceProvider
{

    public function boot(): void
    {

        config()->set('auth.guards.voyager-2fa-login', [
            'driver' => 'session',
            'provider' => 'admins',
        ]);


        $this->app->singleton('VoyagerGuard', function () {
            return 'voyager-2fa-login';
        });

        // Загрузка переводов из директории ресурсов
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', '2fa');

        // Публикация миграций
        $this->publishes([
            __DIR__.'/../migrations/' => database_path('migrations')
        ], '2fa-migrations');

        // Регистрация переводов
        Lang::addNamespace('2fa', __DIR__.'/../resources/lang');
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(Commands\InstallCommand::class);
        }
    }
}