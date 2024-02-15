<?php

return [
    'guards' => [
        'voyager-2fa-login' => [
            'driver' => 'session',
            'provider' => 'admins', // Замените 'admins' на ваш провайдер, если это необходимо
        ],
    ],
];