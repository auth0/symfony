<?php

declare(strict_types=1);

namespace Auth0\Symfony\Exceptions;

use Auth0\Symfony\Contracts\Exceptions\ExceptionInterface;

final class UnsupportedUserException extends \Exception implements ExceptionInterface
{
    public const MSG_CLASS_NOT_SUPPORTED = 'Instances of `%s` are not supported.';

    public static function classNotSupported(
        string $class,
        ?\Throwable $previous = null
    ): self {
        return new self(sprintf(self::MSG_CLASS_NOT_SUPPORTED, $class), 0, $previous);
    }
}
