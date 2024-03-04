<?php

declare(strict_types=1);

namespace Auth0\Symfony\Models;

use Auth0\Symfony\Contracts\Models\UserInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

use function array_key_exists;
use function is_array;
use function is_bool;
use function is_int;
use function is_string;

class User implements SymfonyUserInterface, UserInterface
{
    /**
     * @var array<string>
     */
    protected array $roleAuthenticatedUsing = [];

    /**
     * @var array<string>
     */
    protected array $roles = ['IS_AUTHENTICATED_FULLY', 'ROLE_USER'];

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(protected array $data)
    {
    }

    public function eraseCredentials(): void
    {
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAppMetadata(string $name, $default = null)
    {
        if (! isset($this->data['app_metadata']) || ! is_array($this->data['app_metadata'])) {
            return $default;
        }

        if (! array_key_exists($name, $this->data['app_metadata'])) {
            return $default;
        }

        return $this->data['app_metadata'][$name];
    }

    public function getClientId(): ?string
    {
        $clientId = $this->data['clientID'] ?? null;

        if (! is_string($clientId)) {
            return null;
        }

        return $clientId;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        $createdAt = $this->data['created_at'] ?? null;

        if (! is_string($createdAt)) {
            return null;
        }

        return new DateTimeImmutable($createdAt);
    }

    public function getCustomData(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function getEmail(): ?string
    {
        $email = $this->data['email'] ?? null;

        if (! is_string($email)) {
            return null;
        }

        return $email;
    }

    public function getFamilyName(): ?string
    {
        $familyName = $this->data['family_name'] ?? null;

        if (! is_string($familyName)) {
            return null;
        }

        return $familyName;
    }

    public function getGivenName(): ?string
    {
        $givenName = $this->data['given_name'] ?? null;

        if (! is_string($givenName)) {
            return null;
        }

        return $givenName;
    }

    public function getId(): ?string
    {
        $id = $this->data['user_id'] ?? null;

        if (! is_string($id)) {
            return null;
        }

        return $id;
    }

    /**
     * @return array<mixed>
     */
    public function getIdentities(): array
    {
        $identities = $this->data['identities'] ?? [];

        if (! is_array($identities)) {
            return [];
        }

        return $identities;
    }

    public function getLastIp(): ?string
    {
        $lastIp = $this->data['last_ip'] ?? null;

        if (! is_string($lastIp)) {
            return null;
        }

        return $lastIp;
    }

    public function getLastLoginAt(): ?DateTimeInterface
    {
        $lastLoginAt = $this->data['last_login'] ?? null;

        if (! is_string($lastLoginAt)) {
            return null;
        }

        return new DateTimeImmutable($lastLoginAt);
    }

    public function getLastPasswordResetAt(): ?DateTimeInterface
    {
        $lastPasswordResetAt = $this->data['last_password_reset'];

        if (! is_string($lastPasswordResetAt)) {
            return null;
        }

        return new DateTimeImmutable($lastPasswordResetAt);
    }

    public function getLoginsCount(): int
    {
        $loginsCount = $this->data['logins_count'] ?? 0;

        if (! is_int($loginsCount)) {
            return 0;
        }

        return $loginsCount;
    }

    public function getMultifactor(): ?string
    {
        $multifactor = $this->data['multifactor'] ?? null;

        if (! is_string($multifactor)) {
            return null;
        }

        return $multifactor;
    }

    public function getName(): ?string
    {
        $name = $this->data['name'] ?? $this->data['nickname'] ?? null;

        if (! is_string($name)) {
            return null;
        }

        return $name;
    }

    public function getNickname(): ?string
    {
        $nickname = $this->data['nickname'] ?? null;

        if (! is_string($nickname)) {
            return null;
        }

        return $nickname;
    }

    public function getPhoneNumber(): ?string
    {
        $phoneNumber = $this->data['phone_number'] ?? null;

        if (! is_string($phoneNumber)) {
            return null;
        }

        return $phoneNumber;
    }

    public function getPicture(): ?string
    {
        $picture = $this->data['picture'] ?? null;

        if (! is_string($picture)) {
            return null;
        }

        return $picture;
    }

    /**
     * @return array<int,string>
     *
     * @psalm-suppress RedundantFunctionCall
     */
    public function getRoles(): array
    {
        $response = [];
        $roles = $this->roles;
        $permissions = $this->data['permissions'] ?? [];
        $scopes = $this->data['scope'] ?? [];

        if (is_string($scopes)) {
            $scopes = explode(' ', $scopes);
        }

        foreach ($roles as $role) {
            $response[] = str_replace([':', '-'], '_', strtoupper($role));
        }

        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if (is_string($permission)) {
                    $response[] = 'ROLE_' . str_replace([':', '-'], '_', strtoupper($permission));
                }
            }
        }

        if (is_array($scopes)) {
            foreach ($scopes as $scope) {
                if (is_string($scope)) {
                    $response[] = 'ROLE_' . str_replace([':', '-'], '_', strtoupper($scope));
                }
            }
        }

        foreach ($this->roleAuthenticatedUsing as $using) {
            $response[] = $using;
        }

        return array_unique(array_values($response));
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        $updatedAt = $this->data['updated_at'] ?? null;

        if (! is_string($updatedAt)) {
            return null;
        }

        return new DateTimeImmutable($updatedAt);
    }

    public function getUserIdentifier(): string
    {
        $userIdentifier = $this->getId() ?? $this->data['sub'];

        if (! is_string($userIdentifier)) {
            return '';
        }

        return $userIdentifier;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getUserMetadata(string $name, $default = null)
    {
        if (! isset($this->data['user_metadata']) || ! is_array($this->data['user_metadata'])) {
            return $default;
        }

        if (! array_key_exists($name, $this->data['user_metadata'])) {
            return $default;
        }

        return $this->data['user_metadata'][$name];
    }

    public function getUsername(): ?string
    {
        $username = $this->data['username'] ?? null;

        if (! is_string($username)) {
            return null;
        }

        return $username;
    }

    public function isBlocked(): ?bool
    {
        $blocked = $this->data['blocked'] ?? null;

        if (! is_bool($blocked)) {
            return null;
        }

        return $blocked;
    }

    public function isEmailVerified(): bool
    {
        return filter_var($this->data['email_verified'], FILTER_VALIDATE_BOOLEAN);
    }
}
