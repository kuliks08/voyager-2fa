<?php

namespace Kuliks08\TwoFA;

use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager as Voyager;
use Voyager\Admin\Manager\Plugins as PluginManager;

class TwoFAServiceProvider extends ServiceProvider
{
    public function boot(PluginManager $pluginmanager)
    {
        $pluginmanager->addPlugin(\Kuliks08\TwoFA\TwoFA::class);

        $this->loadTranslationsFrom(realpath(__DIR__.'/../resources/lang'), '2fa');
        Voyager::addTranslations('2fa', '2fa');

        $this->publishes([
            __DIR__.'/../migrations/' => database_path('migrations')
        ], '2fa-migrations');
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(Commands\InstallCommand::class);
        }
    }
}