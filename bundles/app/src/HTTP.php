<?php

namespace Project\App;

class HTTP extends \PHPixie\DefaultBundle\HTTP
{
    protected $classMap = array(
        'messages' => 'Project\App\HTTP\Messages'
    );
}