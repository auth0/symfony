<?php

declare(strict_types=1);

namespace Auth0\Symfony\Exceptions;

use Auth0\Symfony\Contracts\Exceptions\ExceptionInterface;
use Exception;
use Throwable;

final class TokenException extends Exception implements ExceptionInterface
{
    public const MSG_BAD_AUTHORIZATION_HEADER = "The request's `Authorization` header did not include a valid bearer token.";

    public const MSG_MISSING_AUTHORIZATION_HEADER = 'An `Authorization` header was not found in the request.';

    public static function badAuthorizationHeader(
        ?Throwable $previous = null,
    ): self {
        return new self(self::MSG_BAD_AUTHORIZATION_HEADER, 0, $previous);
    }

    public static function missingAuthorizationHeader(
        ?Throwable $previous = null,
    ): self {
        return new self(self::MSG_MISSING_AUTHORIZATION_HEADER, 0, $previous);
    }
}
