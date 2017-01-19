<?php

namespace Project\App\ORM\Model;

use PHPixie\ORM\Models\Type\Database\Repository as DatabaseRepository;
use PHPixie\ORM\Wrappers\Type\Database\Repository as DatabaseRepositoryWrapper;
use Project\App\AppBuilder;

abstract class Repository extends DatabaseRepositoryWrapper
{
    /**
     * @var AppBuilder
     */
    protected $builder;

    /**
     * @param DatabaseRepository $repository
     * @param AppBuilder $builder
     */
    public function __construct($repository, $builder)
    {
        $this->builder = $builder;
        parent::__construct($repository);
    }
}
