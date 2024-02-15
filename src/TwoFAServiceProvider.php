<?php

namespace Kuliks08\TwoFA;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\Http\Kernel;

class TwoFAServiceProvider extends ServiceProvider
{
    public function boot(Kernel $kernel)
    {
        $kernel->pushMiddleware(\Kuliks08\TwoFA\TwoFA::class);

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