<?php

declare(strict_types=1);

namespace Auth0\Symfony\Models\Stateless;

use Auth0\Symfony\Contracts\Models\Stateless\UserInterface;

class User extends \Auth0\Symfony\Models\User implements UserInterface
{
    /**
     * @var array<string>
     */
    protected array $roleAuthenticatedUsing = ['ROLE_USING_TOKEN'];
}
