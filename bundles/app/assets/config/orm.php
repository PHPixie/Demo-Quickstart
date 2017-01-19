<?php

return [
    'relationships' => [

        // Each user may have multiple messages
        [
            'type'  => 'oneToMany',
            'owner' => 'user',
            'items' => 'message'
        ]
    ]
];