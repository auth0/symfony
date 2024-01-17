<?php

declare(strict_types=1);

namespace Auth0\Tests\Unit\Models;

use Auth0\Symfony\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

    /** @param string[] $expectedRoles */
    private function assertHasRoles(User $user, array $expectedRoles): void
    {
        $userRoles = $user->getRoles();
        foreach ($expectedRoles as $role) {
            $this::assertContains($role, $userRoles);
        }
    }

    public function testGetRolesWithSingleScope(): void
    {
        $user = new User([
            'scope' => 'read:users',
        ]);

        $this->assertHasRoles($user, ['ROLE_USER', 'ROLE_READ_USERS']);
    }

    public function testGetRolesWithArrayScope(): void
    {
        $user = new User([
            'scope' => ['read:users', 'write:users'],
        ]);

        $this->assertHasRoles($user, ['ROLE_USER', 'ROLE_READ_USERS', 'ROLE_WRITE_USERS']);
    }

    public function testGetRolesWithStringScope(): void
    {
        $user = new User([
            'scope' => 'read:users write:users',
        ]);

        $this->assertHasRoles($user, ['ROLE_USER', 'ROLE_READ_USERS', 'ROLE_WRITE_USERS']);
    }
}
