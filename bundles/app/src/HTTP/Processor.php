<?php

namespace Project\App\HTTP;

use Project\App\AppBuilder;
use Project\App\ORM\User;

/**
 * Your base web processor class
 */
abstract class Processor extends \PHPixie\DefaultBundle\HTTP\Processor
{
    /**
     * @var AppBuilder
     */
    protected $builder;

    /**
     * @param AppBuilder $builder
     */
    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     * Return a user if he is logged in, or null otherwise
     *
     * @return User|null
     */
    protected function user()
    {
        $domain = $this->components()->auth()->domain();
        return $domain->user();
    }
}