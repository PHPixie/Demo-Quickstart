<?php

namespace Project\App\ORM\User;

use Project\App\ORM\Model\Repository;
use Project\App\ORM\User;

/** This interface allows password login integration */
use PHPixie\AuthLogin\Repository as LoginUserRepository;

/**
 * ORM User repository
 */
class UserRepository extends Repository implements LoginUserRepository
{
    /**
     * Find a user by id, or return null if it does not exist.
     * This method is required for any login.
     * @param mixed $id
     * @return User|null
     */
    public function getById($id)
    {
        return $this->query()
            ->in($id)
            ->findOne();
    }

    /**
     * Find a user by 'login', or return null if it does not exist.
     * This method is required for password login.
     *
     * Note that this method might search by multiple fields
     * and allow login for example with both username or email.
     * @param mixed $login
     * @return User|null
     */
    public function getByLogin($login)
    {
        return $this->query()
            ->where('email', $login)
            ->findOne();
    }
}
