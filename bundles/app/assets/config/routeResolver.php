<?php

return array(
    'type'      => 'group',
    'defaults'  => array('action' => 'default'),
    'resolvers' => array(

        // Two routes for social login redirect and callback
        'socialAuth' => array(
            'path' => 'socialAuth(/<provider>)',
            'defaults' => ['processor' => 'socialAuth']
        ),

        'socialAuthCallback' => array(
            'path' => 'socialAuth/callback/<provider>',
            'defaults' => ['processor' => 'socialAuth', 'action' => 'callback']
        ),

        // We add a custom 'page' parameter that is used by the pagination
        'messages' => array(
            'path' => 'page(/<page>)',
            'defaults' => ['processor' => 'messages']
        ),

        'action' => array(
            'path' => '<processor>/<action>'
        ),

        'processor' => array(
            'path'     => '(<processor>)',
            'defaults' => array('processor' => 'messages')
        ),

        // We add a shorthand route to the frontpage
        'frontpage' => array(
            'path' => '',
            'defaults' => ['processor' => 'messages']
        )
    )
);
