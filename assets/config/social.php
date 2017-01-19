<?php

return [
    'facebook' => [
        'type'      => 'facebook',

        // Referencing parameters from /assets/parameters.php
        'appId'     => '%social.facebookId%',
        'appSecret' => '%social.facebookSecret%'
    ],

    'twitter' => [
        'type'      => 'twitter',
        'consumerKey'    => '%social.twitterId%',
        'consumerSecret' => '%social.twitterSecret%'
    ]
];