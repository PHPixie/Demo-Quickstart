<?php

return [
    'domains' => [
        'default' => [

            // use the ORM user repository for users
            'repository' => 'framework.orm.user',

            // Here we define the ways that the users can login
            'providers'  => [

                // We need sessions to remember the user after login
                'session' => [
                    'type' => 'http.session'
                ],

                // Allow logging in with password
                'password' => [
                    'type' => 'login.password',

                    // after login persis the user in the session
                    'persistProviders' => ['session']
                ],

                // Enable social login
                'social' => [
                    'type' => 'social.oauth',

                    // after login persis the user in the session
                    'persistProviders' => ['session']
                ]
            ]
        ]
    ]
];