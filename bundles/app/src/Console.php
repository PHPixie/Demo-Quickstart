<?php

namespace Project\App;

class Console extends \PHPixie\DefaultBundle\Console
{
    /**
     * Here we define console commands
     * @var array
     */
    protected $classMap = array(
        'messages' => 'Project\App\Console\Messages',
        'stats'    => 'Project\App\Console\Stats'
    );
}