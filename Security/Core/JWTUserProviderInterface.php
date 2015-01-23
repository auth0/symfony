<?php
/**
 * Created by PhpStorm.
 * User: german
 * Date: 1/22/15
 * Time: 9:20 PM
 */

namespace Auth0\JWTAuthBundle\Security\Core;

use Symfony\Component\Security\Core\User\UserProviderInterface;

interface JWTUserProviderInterface extends UserProviderInterface{

    /**
     * Loads the user for the given decoded JWT.
     *
     * This method must throw JWTInfoNotFoundException if the user is not
     * found.
     *
     * @param string $jwt The decoded Json Web Token
     *
     * @return UserInterface
     *
     * @see JWTInfoNotFoundException
     *
     * @throws JWTInfoNotFoundException if the user is not found
     */
    public function loadUserByJWT($jwt);

} 