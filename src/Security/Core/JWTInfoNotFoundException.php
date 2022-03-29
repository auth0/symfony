<?php

declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security\Core;

/**
 * AuthenticationException exception when JWT is missing.
 */
class JWTInfoNotFoundException extends \Symfony\Component\Security\Core\Exception\AuthenticationException
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getMessageKey(): string
    {
        return 'JWT could not be found.';
    }
}
