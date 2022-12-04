<?php

declare(strict_types=1);

namespace Auth0\Symfony\Contracts\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserProviderInterface
{
    public function loadUserToken(\stdClass $jwt): UserInterface;
}
