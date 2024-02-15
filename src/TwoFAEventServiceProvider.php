<?php

namespace Kuliks08\TwoFA;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Kuliks08\TwoFA\Listeners\TwoFARoute;

class TwoFAEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \TCG\Voyager\Events\RoutingAfter::class => [
            TwoFARoute::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}