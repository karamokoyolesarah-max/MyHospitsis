<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'patients' => [
            'driver' => 'session',
            'provider' => 'patients',
        ],

        // AJOUT DU SUPERADMIN
        'superadmin' => [
            'driver' => 'session',
            'provider' => 'super_admins',
        ],

        'medecin_externe' => [
            'driver' => 'session',
            'provider' => 'medecins_externes',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'patients' => [
            'driver' => 'eloquent',
            'model' => App\Models\Patient::class,
        ],

        // AJOUT DU PROVIDER POUR LE MODÈLE SUPERADMIN
        'super_admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\SuperAdmin::class,
        ],

        'medecins_externes' => [
            'driver' => 'eloquent',
            'model' => App\Models\MedecinExterne::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'patients' => [
            'provider' => 'patients',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        
        // OPTIONNEL : Reset password pour superadmin
        'super_admins' => [
            'provider' => 'super_admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'medecins_externes' => [
            'provider' => 'medecins_externes',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];