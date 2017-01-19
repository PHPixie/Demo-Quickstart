<?php

namespace Project\App;

/**
 * Here you can define wrappers for the ORM to use.
 */
class ORM extends \PHPixie\DefaultBundle\ORM
{
    /**
     * Here we map ORM entities to their wrappers
     * @var array
     */
    protected $entityMap = array(
        'user' => 'Project\App\ORM\User'
    );

    /**
     * Here we map ORM repositories to their wrappers
     * @var array
     */
    protected $repositoryMap = [
        'user' => 'Project\App\ORM\User\UserRepository'
    ];
}