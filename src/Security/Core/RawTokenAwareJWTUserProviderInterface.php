<?php

namespace Auth0\JWTAuthBundle\Security\Core;

/**
 * Gives access to the raw JWT.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface RawTokenAwareJWTUserProviderInterface extends JWTUserProviderInterface
{
    /**
     * {@inheritDoc}
     *
     * @param string|null $rawToken The raw (not decoded) token.
     */
    public function loadUserByJWT($jwt, $rawToken = null);
}
