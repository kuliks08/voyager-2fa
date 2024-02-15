<?php

namespace Kuliks08\TwoFA\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Route;

class TwoFARoute
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $namespacePrefix = '\\'.'Kuliks08\TwoFA\Http\Controllers'.'\\';
        Route::post('login', ['uses' => $namespacePrefix.'TwoFAAuthController@postLogin', 'as' => 'postlogin']);
    }
}