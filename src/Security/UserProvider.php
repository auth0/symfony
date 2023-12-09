<?php

declare(strict_types=1);

namespace Auth0\Symfony\Security;

use const true;

use Auth0\Symfony\Contracts\Models\UserInterface;
use Auth0\Symfony\Contracts\Security\UserProviderInterface;
use Auth0\Symfony\Models\Stateful\User as StatefulUser;
use Auth0\Symfony\Models\Stateless\User as StatelessUser;
use Auth0\Symfony\Models\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\{UserInterface as SymfonyUserInterface, UserProviderInterface as SymfonyUserProviderInterface};

final class UserProvider implements SymfonyUserProviderInterface, UserProviderInterface
{
    public function loadByUserModel(User $user): SymfonyUserInterface
    {
        return $user;
    }

    public function loadUserByIdentifier(string $identifier): SymfonyUserInterface
    {
        $identifier = json_decode($identifier, true);

        if ('stateful' === $identifier['type']) {
            return new StatefulUser($identifier['data']['user']);
        }

        return new StatelessUser($identifier['data']['user']);
    }

    public function refreshUser(SymfonyUserInterface $user): SymfonyUserInterface
    {
        if (! $user instanceof UserInterface) {
            throw new UnsupportedUserException();
        }

        return $user;
    }

    public function supportsClass($class): bool
    {
        return $class instanceof UserInterface || is_subclass_of($class, UserInterface::class);
    }
}
