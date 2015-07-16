<?php

namespace Project\App\ORMWrappers;

class Project extends \PHPixie\ORM\Wrappers\Type\Database\Entity
{
    //We add a simple method that will tell us
    //whether the project is considered done
    public function isDone()
    {
        return $this->tasksDone === $this->tasksTotal;
    }
}