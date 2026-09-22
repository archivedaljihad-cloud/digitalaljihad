<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used to hash
    | passwords for your application. By default, the bcrypt algorithm is
    | used; however, you remain free to modify this option if you wish.
    |
    | Supported: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => (!empty(env('HASH_DRIVER')) ? env('HASH_DRIVER') : 'bcrypt'),

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Bcrypt algorithm. This will allow you
    | to control the amount of time it takes to hash the given password.
    |
    */

    'bcrypt' => [
        'rounds' => (int) (!empty(env('BCRYPT_ROUNDS')) ? env('BCRYPT_ROUNDS') : 10),
        'verify' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Argon algorithm. These will allow you
    | to control the amount of time it takes to hash the given password.
    |
    */

    'argon' => [
        'memory' => (int) (!empty(env('ARGON_MEMORY')) ? env('ARGON_MEMORY') : 1024),
        'threads' => (int) (!empty(env('ARGON_THREADS')) ? env('ARGON_THREADS') : 2),
        'time' => (int) (!empty(env('ARGON_TIME')) ? env('ARGON_TIME') : 2),
        'verify' => true,
    ],

];
