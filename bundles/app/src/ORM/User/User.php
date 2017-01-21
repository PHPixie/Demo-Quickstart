<?php

namespace Project\App\ORM;

use Project\App\ORM\Model\Entity;

/** This interface allows password login integration */
use PHPixie\AuthLogin\Repository\User as LoginUser;

/**
 * ORM user entity
 */
class User extends Entity implements LoginUser
{
    /**
     * Returns the users' hashed password.
     * This method is required for password login.
     *
     * In our case it just returns the value of the database 'passwordHash' field.
     * @return string|null
     */
    public function passwordHash()
    {
        return $this->getField('passwordHash');
    }
}
