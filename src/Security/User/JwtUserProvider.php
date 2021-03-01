<?php declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security\User;

use stdClass;
use Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface;
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
     *
     * @param mixed $class A user class type.
     *
     * @return boolean
     */
    public function supportsClass($class)
    {
        return $class === User::class;
    }

    /**
     * {@inheritdoc}
     *
     * @param stdClass $jwt An encoded JWT.
     *
     * @return User
     */
    public function loadUserByJWT(stdClass $jwt)
    {
        $token = null;

        if (isset($jwt->token)) {
            $token = $jwt->token;
        }

        return new User($jwt->sub, $token, $this->getRoles($jwt));
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
     * @return UserInterface|void
     *
     * @throws UsernameNotFoundException When attempting to load a user by username. Use the loadUserByJWT instead.
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
     *
     * @param UserInterface $user An instance of a User.
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException When provided an incompatible User instance.
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
     * @param stdClass $jwt An encoded JWT.
     *
     * @return array<string>
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
     * @param stdClass $jwt An encoded JWT.
     *
     * @return array<string>
     */
    private function getScopesFromJwtAsRoles(stdClass $jwt)
    {
        if (isset($jwt->scope) === false) {
            return [];
        }

        $scopes = explode(' ', $jwt->scope);
        $roles  = array_map(
            function ($scope) {
                $roleSuffix = strtoupper(str_replace([':', '-'], '_', $scope));

                return sprintf('ROLE_JWT_SCOPE_%s', $roleSuffix);
            },
            $scopes
        );

        return $roles;
    }
}
