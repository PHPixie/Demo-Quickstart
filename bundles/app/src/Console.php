<?php

namespace Project\App;

class Console extends \PHPixie\DefaultBundle\Console
{
    protected $classMap = array(
        'greet' => 'Project\App\Console\Greet'
    );
}