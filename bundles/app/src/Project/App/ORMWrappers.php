<?php

namespace Project\App;

class ORMWrappers extends \PHPixie\ORM\Wrappers\Implementation
{
    //declare wrapped entities
    protected $databaseEntities = array(
        'project'
    );

    public function projectEntity($entity)
    {
        return new ORMWrappers\Project($entity);
    }
}