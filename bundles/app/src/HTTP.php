<?php

namespace Project\App;

class HTTP extends \PHPixie\DefaultBundle\HTTP
{
    /**
     * Here we define HTTP processors (e.g. our controllers)
     * @var array
     */
    protected $classMap = array(
        'messages'   => 'Project\App\HTTP\Messages',
        'auth'       => 'Project\App\HTTP\Auth'
    );
}