<?php

declare(strict_types=1);

namespace Auth0\Symfony\Exceptions;

use Auth0\Symfony\Contracts\Exceptions\ExceptionInterface;

final class AuthenticationException extends \Exception implements ExceptionInterface
{
    public const MSG_NOT_AUTHENTICATED = 'Request is not authenticated';

    public static function notAuthenticated(
        ?\Throwable $previous = null
    ): self {
        return new self(self::MSG_NOT_AUTHENTICATED, 0, $previous);
    }
}
