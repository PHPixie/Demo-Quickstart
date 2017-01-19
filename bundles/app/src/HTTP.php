<?php

namespace Project\App;

class HTTP extends \PHPixie\DefaultBundle\HTTP
{
    protected $classMap = array(
        'greet' => 'Project\App\HTTP\Greet'
    );
}