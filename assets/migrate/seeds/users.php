<?php

return [
    [
        'id' => 1,
        'name' => 'Pixie',
        'email' => 'pixie@phpixie.com',

        // Password is '1', the hash can be generated like this:
        // password_hash('1', PASSWORD_DEFAULT);
        'passwordHash' => '$2y$10$rYHbaIdHJPGbaG8cgSsV9uplT5Mjr2aFAAGxFX50auip6W/Qv58Y.'
    ],

    [
        'id' => 2,
        'name' => 'Trixie',
        'email' => 'trixie@phpixie.com',
        'passwordHash' => '$2y$10$rYHbaIdHJPGbaG8cgSsV9uplT5Mjr2aFAAGxFX50auip6W/Qv58Y.'
    ]
];