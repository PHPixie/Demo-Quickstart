<?php

namespace Project\App\ORM\Model;

use PHPixie\ORM\Models\Type\Database\Entity as DatabaseEntity;
use PHPixie\ORM\Wrappers\Type\Database\Entity as DatabaseEntityWrapper;
use Project\App\AppBuilder;

abstract class Entity extends DatabaseEntityWrapper
{
    /**
     * @var AppBuilder
     */
    protected $builder;

    /**
     * @param DatabaseEntity $entity
     * @param AppBuilder $builder
     */
    public function __construct($entity, $builder)
    {
        $this->builder = $builder;
        parent::__construct($entity);
    }
}
