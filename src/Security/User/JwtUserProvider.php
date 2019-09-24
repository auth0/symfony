<?php

namespace Auth0\JWTAuthBundle\Security\User;

use Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface;
use stdClass;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Basic JWT UserProvider implementation when you do not require loading the user from the database and
 * the JWT verification with Auth0 is enough for your use-case. Eg. Machine-to-Machine authentication.
 */
class JwtUserProvider implements JWTUserProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === User::class;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByJWT($jwt)
    {
        $token = null;
        if (isset($jwt->token)) {
            $token = $jwt->token;
        }

        return new User($jwt->sub, $token, $this->getRoles($jwt));
    }

    /**
     * Unused by the @see JwtGuardAuthenticator.
     */
    public function getAnonymousUser()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        throw new UsernameNotFoundException(
            sprintf(
                '%1$s cannot load user "%2$s" by username. Use %1$s::loadUserByJWT instead.',
                __CLASS__,
                $username
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if ($user instanceof User === false) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return new User($user->getUsername(), $user->getPassword(), $user->getRoles());
    }

    /**
     * Returns the roles for the user.
     *
     * @param stdClass $jwt
     *
     * @return array
     */
    private function getRoles(stdClass $jwt)
    {
        return array_merge(
            [
                'ROLE_JWT_AUTHENTICATED',
            ],
            $this->getScopesFromJwtAsRoles($jwt)
        );
    }

    /**
     * Returns the scopes from the JSON Web Token as Symfony roles prefixed with 'ROLE_JWT_SCOPE_'.
     *
     * @param stdClass $jwt
     *
     * @return array
     */
    private function getScopesFromJwtAsRoles(stdClass $jwt)
    {
        if (isset($jwt->scope) === false) {
            return [];
        }

        $scopes = explode(' ', $jwt->scope);
        $roles = array_map(
            function ($scope) {
                $roleSuffix = strtoupper(str_replace([':', '-'], '_', $scope));

                return sprintf('ROLE_JWT_SCOPE_%s', $roleSuffix);
            },
            $scopes
        );

        return $roles;
    }
}
