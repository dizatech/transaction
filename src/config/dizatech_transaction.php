<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | This value determines which of the following gateway to use.
    | You can switch to a different driver at runtime.
    |
    */
    'default' => 'parsian',

    /*
    |--------------------------------------------------------------------------
    | Drivers Information
    |--------------------------------------------------------------------------
    |
    | These are the list of drivers information to use in package.
    | You can change the information.
    |
    */
    'information' => [
        'parsian' => [
            'constructor' => [
                'pin' => ''
            ],
            'options' => [

            ]
        ],
        'pasargad' => [
            'constructor'   => [
                'username'          => env('PASARGAD_IPG_USERNAME'),
                'password'          => env('PASARGAD_IPG_PASSWORD'),
                'terminal_number'   => env('PASARGAD_IPG_TERMINAL'),
            ],
            'options'       => [
                'verifySSL'         => true
            ]
        ],
        'sadad' => [
            'constructor'   => [
                'merchant_id'   => env('SADAD_MERCHANT_ID'),
                'terminal_id'   => env('SADAD_TERMINAL_ID'),
                'key'           => env('SADAD_KEY'),
            ],
        ],
        'zarinpal' => [
            'constructor' => [
                'merchantId'    => ''
            ]
        ],
        'mahamax' => [
            'constructor'   => [
                'base_url' => env('MAHAMAX_IPG_URL'),
            ],
        ],
        'saman' => [
            'constructor'   => [
                'terminal_id'   => env('SAMAN_TERMINAL_ID'),
                'password'      => env('SAMAN_PASSWORD'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | List of Drivers
    |--------------------------------------------------------------------------
    |
    | These are the list of drivers to use for this package.
    | You can change the name.
    |
    */
    'drivers' => [
        'parsian'   => \Dizatech\Transaction\Drivers\Parsian::class,
        'pasargad'  => \Dizatech\Transaction\Drivers\Pasargad::class,
        'zarinpal'  => \Dizatech\Transaction\Drivers\Zarinpal::class,
        'sadad'     => \Dizatech\Transaction\Drivers\Sadad::class,
        'mahamax'   => \Dizatech\Transaction\Drivers\Mahamax::class,
        'saman'     => \Dizatech\Transaction\Drivers\Saman::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | A Model that has relation with transaction, Order or Payment or ..
    |--------------------------------------------------------------------------
    |
    | Set the namespace of your model that has relation with Transaction.
    |
    */
    'model' => ''
];
