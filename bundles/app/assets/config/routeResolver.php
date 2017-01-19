<?php

return array(
    'type'      => 'group',
    'defaults'  => array('action' => 'default'),
    'resolvers' => array(
        
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
