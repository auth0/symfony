<?php

declare(strict_types=1);

namespace Auth0\Symfony\Models;

use Auth0\Symfony\Contracts\Models\UserInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

use function array_key_exists;
use function is_string;

final class User implements SymfonyUserInterface, UserInterface
{
    private array $roleAuthenticatedUsing = [];

    private array $roles = ['IS_AUTHENTICATED_FULLY', 'ROLE_USER'];

    public function __construct(private array $data)
    {
    }

    public function eraseCredentials(): void
    {
    }

    public function getAppMetadata(string $name, $default = null)
    {
        if (! isset($this->data['app_metadata'])) {
            return $default;
        }

        if (! array_key_exists($name, $this->data['app_metadata'])) {
            return $default;
        }

        return $this->data['app_metadata'][$name];
    }

    public function getClientId(): ?string
    {
        return $this->data['clientID'] ?? null;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return isset($this->data['created_at']) ? new DateTimeImmutable($this->data['created_at']) : null;
    }

    public function getCustomData(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->data['email'] ?? null;
    }

    public function getFamilyName(): ?string
    {
        return $this->data['family_name'] ?? null;
    }

    public function getGivenName(): ?string
    {
        return $this->data['given_name'] ?? null;
    }

    public function getId(): ?string
    {
        return $this->data['user_id'] ?? null;
    }

    public function getIdentities(): array
    {
        return $this->data['identities'] ?? [];
    }

    public function getLastIp(): ?string
    {
        return $this->data['last_ip'] ?? null;
    }

    public function getLastLoginAt(): ?DateTimeInterface
    {
        return isset($this->data['last_login']) ? new DateTimeImmutable($this->data['last_login']) : null;
    }

    public function getLastPasswordResetAt(): ?DateTimeInterface
    {
        return isset($this->data['last_password_reset']) ? new DateTimeImmutable($this->data['last_password_reset']) : null;
    }

    public function getLoginsCount(): int
    {
        return $this->data['logins_count'] ?? 0;
    }

    public function getMultifactor(): ?string
    {
        return $this->data['multifactor'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? $this->data['nickname'] ?? null;
    }

    public function getNickname(): ?string
    {
        return $this->data['nickname'] ?? null;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->data['phone_number'] ?? null;
    }

    public function getPicture(): ?string
    {
        return $this->data['picture'] ?? null;
    }

    public function getRoles(): array
    {
        $response = [];
        $roles = $this->roles;
        $permissions = $this->data['permissions'] ?? [];
        $scopes = $this->data['scope'] ?? [];

        if (is_string($scopes)) {
            $scopes = [$scopes];
        }

        foreach ($roles as $role) {
            $response[] = implode('_', explode(':', strtoupper($role)));
        }

        foreach ($permissions as $permission) {
            $response[] = 'ROLE_' . implode('_', explode(':', strtoupper($permission)));
        }

        foreach ($scopes as $scope) {
            $response[] = 'ROLE_' . implode('_', explode(':', strtoupper($scope)));
        }

        $response[] = $this->roleAuthenticatedUsing;

        return array_unique(array_values($response));
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return isset($this->data['updated_at']) ? new DateTimeImmutable($this->data['updated_at']) : null;
    }

    public function getUserIdentifier(): string
    {
        return $this->getId() ?? $this->data['sub'];
    }

    public function getUserMetadata(string $name, $default = null)
    {
        if (! isset($this->data['user_metadata'])) {
            return $default;
        }

        if (! array_key_exists($name, $this->data['user_metadata'])) {
            return $default;
        }

        return $this->data['user_metadata'][$name];
    }

    public function getUsername(): ?string
    {
        return $this->data['username'] ?? null;
    }

    public function isBlocked(): ?bool
    {
        return $this->data['blocked'] ?? null;
    }

    public function isEmailVerified(): bool
    {
        return filter_var($this->data['email_verified'], FILTER_VALIDATE_BOOLEAN) ?? false;
    }
}
