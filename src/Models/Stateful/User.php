<?php

declare(strict_types=1);

namespace Auth0\Symfony\Models\Stateful;

use Auth0\Symfony\Contracts\Models\Stateful\UserInterface;

class User extends \Auth0\Symfony\Models\User implements UserInterface
{
    protected $roleAuthenticatedUsing = 'ROLE_USING_SESSION';
}
