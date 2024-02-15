<?php

return [
    'guards' => array_merge(
        config('auth.guards'), // сохраняем существующие сторожи
        [
            'voyager-2fa-login' => [
                'driver' => 'session',
                'provider' => 'admins', // Замените 'admins' на ваш провайдер, если это необходимо
            ],
        ]
    ),
];
