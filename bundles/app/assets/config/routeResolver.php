<?php

return array(
    //this allows us to group routes into one
    'type'      => 'group',
    'resolvers' => array(

        //...We will add new routes here..
        
        'view' => array(
            'type'     => 'pattern',

            //Since the id parameter is mandatory
            //we don't wrap it in brackets
            'path'     => 'quickstart/view/<id>',
            'defaults' => array(
                'processor' => 'quickstart',
                'action'    => 'view'
            )
        ),
        
        //The 'default' route
        'default' => array(
        //this type of route does pattern matching
            'type'     => 'pattern',

            //brackets mean that the part in them is optional
            'path'     => '(<processor>(/<action>))',

            //Default set of parameters to use
            //E.g. if the url is simply /hello
            //The 'action' parameter will default to 'greet'
            'defaults' => array(
                'processor' => 'greet',
                'action'    => 'default'
            )
        )
    )
);