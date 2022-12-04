<?php

declare(strict_types=1);

namespace Auth0\Symfony\Exceptions;

use Auth0\Symfony\Contracts\Exceptions\UserNotFoundExceptionInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException as SymfonyUserNotFoundException;;

final class UserNotFoundException extends SymfonyUserNotFoundException implements UserNotFoundExceptionInterface
{
    public const MSG_CANNOT_LOAD_BY_USERNAME = '%1$s cannot load user "%2$s" by username. Use %1$s::loadUserByJWT instead.';

    public static function loadByUsernameUnsupported(
        string $class,
        string $username,
        ?\Throwable $previous = null
    ): self {
        return new self(sprintf(self::MSG_CANNOT_LOAD_BY_USERNAME, $class, $username), 0, $previous);
    }
}
