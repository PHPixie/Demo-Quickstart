<?php

/**
 * We define some parameters that can be later referenced in the configuration.
 *
 * You can use this to keep all environment specific variables,
 * like database credentials etc. in one file.
 */
return [
    'database' => [
        'name'     => 'phpixie',
        'user'     => 'phpixie',
        'password' => 'phpixie'
    ],

    'social' => [
        'facebookId'     => 'YOUR APP ID',
        'facebookSecret' => 'YOUR APP SECRET',

        'twitterId'     => 'YOUR APP ID',
        'twitterSecret' => 'YOUR APP SECRET',
    ]
];