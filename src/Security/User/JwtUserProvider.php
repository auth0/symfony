<?php

declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security\User;

use Auth0\JWTAuthBundle\Security\Auth0Service;
use Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Basic JWT UserProvider implementation when you do not require loading the user from the database and
 * the JWT verification with Auth0 is enough for your use-case. Eg. Machine-to-Machine authentication.
 */
class JwtUserProvider implements JWTUserProviderInterface
{
    private Auth0Service $auth0Service;

    public function __construct(Auth0Service $auth0Service)
    {
        $this->auth0Service = $auth0Service;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $class A user class type.
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === InMemoryUser::class;
    }

    /**
     * {@inheritdoc}
     *
     * @param \stdClass $jwt An encoded JWT.
     *
     * @return UserInterface
     */
    public function loadUserByJWT(\stdClass $jwt): UserInterface
    {
        $token = $jwt->token ?? null;
        return new InMemoryUser($jwt->sub, $token, $this->getRoles($jwt));
    }

    /**
     * {@inheritdoc}
     *
     * @return null
     */
    public function getAnonymousUser()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $username A string representing the username of a user.
     *
     * @return UserInterface
     *
     * @throws UserNotFoundException When attempting to load a user by username. Use the loadUserByJWT instead.
     */
    public function loadUserByUsername($username): UserInterface
    {
        throw new UserNotFoundException(
            sprintf(
                '%1$s cannot load user "%2$s" by username. Use %1$s::loadUserByJWT instead.',
                self::class,
                $username
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param UserInterface $user An instance of a User.
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException When provided an incompatible User instance.
     */
    public function refreshUser(UserInterface $user)
    {
        if ($user instanceof InMemoryUser === false) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', $user::class)
            );
        }

        return new InMemoryUser($user->getUserIdentifier(), $user->getPassword(), $user->getRoles());
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserByJWT($this->auth0Service->decodeJWT($identifier));
    }

    /**
     * Returns the roles for the user.
     *
     * @param \stdClass $jwt An encoded JWT.
     *
     * @return string[]
     *
     * @psalm-return non-empty-list<string>
     */
    private function getRoles(\stdClass $jwt): array
    {
        $roles = $this->getScopesFromJwtAsRoles($jwt);
        $roles[] = 'ROLE_JWT_AUTHENTICATED';
        return $roles;
    }

    /**
     * Returns the scopes from the JSON Web Token as Symfony roles prefixed with 'ROLE_JWT_SCOPE_'.
     *
     * @param \stdClass $jwt An encoded JWT.
     *
     * @return string[]
     *
     * @psalm-return list<string>
     */
    private function getScopesFromJwtAsRoles(\stdClass $jwt): array
    {
        if (isset($jwt->scope) === false) {
            return [];
        }

        $scopes = explode(' ', $jwt->scope);

        return array_map(
            static function ($scope) {
                $roleSuffix = strtoupper(str_replace([':', '-'], '_', $scope));

                return sprintf('ROLE_JWT_SCOPE_%s', $roleSuffix);
            },
            $scopes
        );
    }
}
