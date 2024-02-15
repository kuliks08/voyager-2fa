<?php

return [
    'guards' => [
        'voyager-2fa-login' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],
];
