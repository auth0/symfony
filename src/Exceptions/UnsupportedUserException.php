<?php

declare(strict_types=1);

namespace Auth0\Symfony\Exceptions;

use Auth0\Symfony\Contracts\Exceptions\UnsupportedUserExceptionInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException as SymfonyUnsupportedUserException;

final class UnsupportedUserException extends SymfonyUnsupportedUserException implements UnsupportedUserExceptionInterface
{
    public const MSG_CLASS_NOT_SUPORTED = 'Instances of `%s` are not supported.';

    public static function classNotSupported(
        string $class,
        ?\Throwable $previous = null
    ): self {
        return new self(sprintf(self::MSG_CLASS_NOT_SUPORTED, $class), 0, $previous);
    }
}
