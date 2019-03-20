<?php

namespace Auth0\JWTAuthBundle\Security\Core;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author german
 */
interface JWTUserProviderInterface extends UserProviderInterface
{
    /**
     * Loads the user for the given decoded JWT.
     *
     * This method must throw JWTInfoNotFoundException if the user is not
     * found.
     *
     * @param object $jwt The decoded Json Web Token
     *
     * @return UserInterface
     *
     * @throws JWTInfoNotFoundException if the user is not found
     */
    public function loadUserByJWT(object $jwt): UserInterface;

    /**
     * Returns an anonymous user
     *
     * This can return a JWTInfoNotFoundException exception if you don't want
     * to handle anonymous users
     *
     * It is recommendeded to return a user with the role IS_AUTHENTICATED_ANONYMOUSLY
     *
     * @return UserInterface
     *
     * @throws JWTInfoNotFoundException if no anonymous user can be used
     */
    public function getAnonymousUser(): UserInterface;

}
