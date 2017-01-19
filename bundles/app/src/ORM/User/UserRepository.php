<?php

namespace Project\App\ORM\User;

use Project\App\ORM\Model\Repository;
use Project\App\ORM\User;
use PHPixie\Social\User as SocialUser;

/** This interface allows password login integration */
use PHPixie\AuthLogin\Repository as LoginUserRepository;

/** This interface allows social login integration */
use PHPixie\AuthSocial\Repository as SocialRepository;

/**
 * ORM User repository
 */
class UserRepository extends Repository implements LoginUserRepository, SocialRepository
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


    /**
     * Find a user by his social data returned by the Social component.
     * If no mathcing user is found it should return null.
     * This method is required for social login.
     *
     * @param SocialUser $socialUser
     * @return User|null
     */
    public function getBySocialUser($socialUser)
    {
        // Get the name of the database field with the social id
        // e.g. twitterId or facebookId
        $providerName = $socialUser->providerName();
        $field = $this->socialIdField($providerName);

        return $this->query()->where($field, $socialUser->id())->findOne();
    }

    /**
     * Get the name of the database field that stores users' social id.
     * In our case we just add 'Id' to provider name.
     *
     * @param string $providerName
     * @return string
     */
    public function socialIdField($providerName)
    {
        return $providerName.'Id';
    }
}
