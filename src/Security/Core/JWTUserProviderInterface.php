<?php

declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security\Core;

use Auth0\SDK\Token;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * An UserProviderInterface adapter for our JWT users.
 */
interface JWTUserProviderInterface extends UserProviderInterface
{
    /**
     * Loads the user for the given decoded JWT.
     *
     * This method must throw JWTInfoNotFoundException if the user is not
     * found.
     *
     * @param Token $jwt The decoded Json Web Token.
     *
     * @throws AuthenticationException If the user is not found.
     */
    public function loadUserByJWT(Token $jwt): UserInterface;
}

