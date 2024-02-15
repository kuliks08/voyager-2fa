<?php

namespace Kuliks08\TwoFA\Http\Controllers;

use TCG\Voyager\Http\Controllers\VoyagerAuthController;
use Illuminate\Http\Request;

class TwoFAAuthController extends VoyagerAuthController
{
    public function postLogin(Request $request){
        $this->validateLogin($request);
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);

        if ($this->guard()->attempt($credentials, $request->has('remember'))) {
            return $this->sendLoginResponse($request);
        }
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function validateLogin(Request $request){
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            '2fa-response' => 'required|2fa',
        ]);
    }
}