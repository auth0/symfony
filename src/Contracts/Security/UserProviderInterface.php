<?php

declare(strict_types=1);

namespace Auth0\Symfony\Contracts\Security;

use Auth0\Symfony\Models\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserProviderInterface
{
    public function loadByUserModel(User $user): UserInterface;
}
