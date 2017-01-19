<?php

namespace Project\App\HTTP;

use Project\App\AppBuilder;

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
}