<?php

declare(strict_types=1);

namespace Auth0\Symfony\Security\User;

use Auth0\Symfony\Contracts\Security\User\UserProviderInterface;
use Auth0\Symfony\Exceptions\UnsupportedUserException;
use Auth0\Symfony\Exceptions\UserNotFoundException;
use Auth0\Symfony\Security\Service;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface as SymfonyUserProviderInterface;

final class UserProvider extends SymfonyUserProviderInterface implements UserProviderInterface
{
    public function __construct(
        private Service $service
    )
    {
    }

    public function supportsClass($class)
    {
        return $class === InMemoryUser::class;
    }

    public function loadUserToken(\stdClass $jwt): UserInterface
    {
        $token = $jwt->token ?? null;
        return new InMemoryUser($jwt->sub, $token, $this->getRoles($jwt));
    }

    public function getAnonymousUser()
    {
        return null;
    }

    public function loadUserByUsername($username): UserInterface
    {
        throw UserNotFoundException::loadByUsernameUnsupported(self::class, $username);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof InMemoryUser) {
            throw UnsupportedUserException::classNotSupported(get_class($user));
        }

        return new InMemoryUser($user->getUserIdentifier(), $user->getPassword(), $user->getRoles());
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserToken($this->auth0Service->decodeJWT($identifier));
    }

    private function getRoles(\stdClass $jwt): array
    {
        $roles = $this->getScopesFromJwtAsRoles($jwt);
        $roles[] = 'ROLE_JWT_AUTHENTICATED';
        return $roles;
    }

    private function getScopesFromJwtAsRoles(\stdClass $jwt): array
    {
        if (!(property_exists($jwt, 'scope') && $jwt->scope !== null)) {
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
