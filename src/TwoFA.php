<?php

namespace Kuliks08\TwoFA;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use PragmaRX\Google2FALaravel\Google2FA;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class TwoFA
{
    private bool $registered = false;

    public $name = 'Voyager 2FA';
    public $description = 'Two-factor authentication for Voyager II';
    public $repository = 'kuliks08/voyager-2fa';
    public $website = 'https://github.com/kuliks08/voyager-2fa';
    public $version = '1.0.0';

    public function provideJS(): string
    {
        return file_get_contents(__DIR__.'/../dist/voyager-2fa.umd.js');
    }

    public function loginComponent(): ?string
    {
        return 'voyager-2fa-login';
    }

    public function authenticate(Request $request): ?array
    {
        if (!$request->get('email', null) || !$request->get('password', null)) {
            return [ __('voyager::auth.error_field_empty') ];
        } else if ($request->has('otp') && empty($request->otp)) {
            return [ __('2fa::2fa.otp_empty') ];
        }

        if (Auth::validate($request->only('email', 'password'))) {
            // Credentials are good
            $user = $this->getUserModel()->where('email', $request->get('email'))->firstOrFail();

            if ($request->has('otp')) {
                if (!Google2FA::verifyKey($user->{$this->get2FAField()}, $request->input('otp'))) {
                    // 2FA code wrong
                    return [ __('2fa::2fa.otp_wrong') ];
                }
                // Code is correct. Login normally
            } else {
                $secret = $user->{$this->get2FAField()};
                if (!is_null($secret) && !empty($secret)) {
                    // Has to enter code
                    return [ __('2fa::2fa.enter_otp') ];
                }
                // 2FA not activated. Login normally
            }
        }

        if (Auth::attempt($request->only('email', 'password'), $request->has('remember'))) {
            $request->session()->regenerate();
            return null;
        }

        return [ __('voyager::auth.login_failed') ];
    }

    public function handleRequest(Request $request, Closure $next): mixed
    {
        if (!$this->registered) {
            auth()->setDefaultDriver($this->guard());
            $this->registered = true;
            Event::dispatch('voyager.auth.registered', $this);
            $this->registerUserMenuItems();
        }

        if ($this->user() && !Auth::guest()) {
            if (!Google2FA::isActivated()) {
                if (Route::currentRouteName() !== 'voyager.voyager-manage-2fa') {
                    if (Voyager::setting('2FA.force_2fa', false)) {
                        return redirect()->route('voyager.voyager-manage-2fa');
                    }
                    if (Voyager::setting('2FA.show_warning', true)) {
                        Voyager::flashMessage(__('2fa::2fa.activate_message', ['url' => route('voyager.voyager-manage-2fa')]), 'yellow');
                    }
                }
            }
            if (Voyager::authorize($this->user(), 'browse', ['voyager'])) {
                return $next($request);
            }
        }

        return redirect()->guest(route('voyager.login'));
    }

    private function get2FAField() {
        return config('google2fa.otp_secret_column', 'google2fa_secret');
    }

    private function getUserModel() {
        return app(config('auth.providers.'.config('guards.'.$this->guard().'.provider', 'users').'.model', null));
    }

    private function registerUserMenuItems() {
        // Проверяем, есть ли уже пункт меню "Manage 2FA", чтобы избежать дублирования
        $existingMenuItem = MenuItem::where('title', 'Manage 2FA')->first();

        if (!$existingMenuItem) {
            // Создаем новый пункт меню только если его еще нет
            Voyager::addAction(
                (new MenuItem([
                    'title' => 'Manage 2FA',
                    'url' => route('voyager.voyager-manage-2fa'), // ваш маршрут для управления 2FA
                    'icon_class' => 'voyager-lock', // иконка, если требуется
                    'parent_id' => null, // если это дочерний пункт меню, установите соответствующий parent_id
                    'order' => 10, // порядковый номер пункта меню
                ]))
            );
        }
    }

}
