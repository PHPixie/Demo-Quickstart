<?php

return [
    // Database configuration
    'default' => [

        // Referencing parameters from /assets/parameters.php
        'database' => '%database.name%',
        'user'     => '%database.user%',
        'password' => '%database.password%',

        'adapter'  => 'mysql',
        'driver'   => 'pdo'
    ]
];