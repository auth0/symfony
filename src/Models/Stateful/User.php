<?php

namespace Auth0\Symfony\Models\Stateful;

use Auth0\Symfony\Contracts\Models\Stateful\UserInterface;

final class User extends \Auth0\Symfony\Models\User implements UserInterface
{
    protected $roleAuthenticatedUsing = 'ROLE_USING_SESSION';
}
