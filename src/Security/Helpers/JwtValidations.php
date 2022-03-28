<?php

declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security\Helpers;

use Auth0\SDK\Exception\InvalidTokenException;

/**
 * JWT validation helper functions.
 *
 * @package Auth0\JWTAuthBundle\Security
 */
class JwtValidations
{
    /**
     * Validate a given JWT's claims.
     *
     * @param array<string,mixed> $claims A key => value pair of JWT claims to validate.
     * @param array<string,mixed> $token  An array representing data from a decoded JWT.
     *
     * @throws InvalidTokenException When a token claim validation fails.
     */
    public static function validateClaims(array $claims = [], array $token = []): bool
    {
        self::validateClaimNonce($claims['nonce'] ?? null, $token);
        self::validateClaimAzp($claims['azp'] ?? null, $token);
        self::validateClaimAud($claims['aud'] ?? null, $token);
        self::validateClaimAud($claims['org_id'] ?? null, $token);

        return true;
    }

    /**
     * Check if a token includes a nonce claim and that it matches.
     *
     * @param string|null $nonce The expected nonce value.
     * @param array<string,mixed> $token An array representing data from a decoded JWT.
     *
     * @throws InvalidTokenException When token claim validation fails.
     */
    public static function validateClaimNonce(?string $nonce = null, array $token = []): bool
    {
        if ($nonce !== null) {
            $tokenNonce = $token['nonce'] ?? null;

            if (! $tokenNonce || ! is_string($tokenNonce)) {
                throw new InvalidTokenException('Nonce (nonce) claim must be a string present');
            }

            if ($tokenNonce !== $nonce) {
                throw new InvalidTokenException(sprintf(
                    'Nonce (nonce) claim mismatch; expected "%s", found "%s"',
                    $nonce,
                    $tokenNonce
                ));
            }
        }

        return true;
    }

    /**
     * Check if a token includes a azp claim and that it matches.
     *
     * @param string|null $azp The expected azp value.
     * @param array<string,mixed> $token An array representing data from a decoded JWT.
     *
     * @throws InvalidTokenException When token claim validation fails.
     */
    public static function validateClaimAzp(?string $azp = null, array $token = []): bool
    {
        if ($azp !== null) {
            $tokenAzp = $token['azp'] ?? null;

            if (! $tokenAzp || ! is_string($tokenAzp)) {
                throw new InvalidTokenException(
                    'Authorized Party (azp) claim must be a string present'
                );
            }

            if ($tokenAzp !== $azp) {
                throw new InvalidTokenException(sprintf(
                    'Authorized Party (azp) claim mismatch; expected "%s", found "%s"',
                    $azp,
                    $tokenAzp
                ));
            }
        }

        return true;
    }

    /**
     * Check if a token includes a audience claim and that it contains an expected value.
     *
     * @param string|null $aud A value expected inside the token audience.
     * @param array<string,mixed> $token An array representing data from a decoded JWT.
     *
     * @throws InvalidTokenException When token claim validation fails.
     */
    public static function validateClaimAud(?string $aud = null, array $token = []): bool
    {
        if ($aud !== null) {
            $tokenAud = $token['aud'] ?? [];

            if (! isset($token['aud'])) {
                throw new InvalidTokenException(
                    'Audience (aud) claim must be a string or array of strings present'
                );
            }

            if (! is_array($tokenAud)) {
                $tokenAud = [ (string) $tokenAud ];
            }

            if (! in_array($aud, $tokenAud)) {
                throw new InvalidTokenException(sprintf(
                    'Audience (aud) claim mismatch; expected "%s"',
                    $aud
                ));
            }
        }

        return true;
    }

    /**
     * Check if a token includes a org_id claim and that it contains an expected value.
     *
     * @param string|null $aud A value expected inside the token audience.
     * @param array<string,mixed> $token An array representing data from a decoded JWT.
     *
     * @throws InvalidTokenException When token claim validation fails.
     */
    public static function validateClaimOrgId(?string $orgId = null, array $token = []): bool
    {
        if ($orgId !== null) {
            $tokenOrgId = $token['org_id'] ?? null;

            if (! $tokenOrgId || ! is_string($tokenOrgId)) {
                throw new InvalidTokenException('Organization Id (org_id) claim must be a string present in the ID token');
            }

            if ($tokenOrgId !== $orgId) {
                throw new InvalidTokenException(sprintf(
                    'Organization Id (org_id) claim value mismatch in the ID token; expected "%s", found "%s"',
                    $orgId,
                    $tokenOrgId
                ));
            }
        }

        return true;
    }

    /**
     * Check if a token includes a audience claim and that it contains an expected value.
     *
     * @param int|null $maxAge The maximum age (in seconds) after auth_time to consider a token valid.
     * @param array<string,mixed> $token  An array representing data from a decoded JWT.
     * @param int $leeway Extra leeway (in seconds) to allow after a token's auth_time + the provided maxAge.
     * @param int|null $now Starting point (in seconds since Unix Epoch) to calculate maxAge + leeway differences from token.
     *
     * @throws InvalidTokenException When token claim validation fails.
     */
    public static function validateAge(?int $maxAge = null, array $token = [], ?int $leeway = 60, ?int $now = null): bool
    {
        if ($maxAge !== null) {
            $tokenAuthTime = $token['auth_time'] ?? null;

            if (! $tokenAuthTime || ! is_int($token['auth_time'])) {
                throw new InvalidTokenException(
                    'Authentication Time (auth_time) claim must be a number present when Max Age (max_age) is specified'
                );
            }

            $now = $now ?? time();
            $leeway = $leeway ?? 60;
            $tokenValidUntil = $tokenAuthTime + $maxAge + $leeway;

            if ($now > $tokenValidUntil) {
                throw new InvalidTokenException(sprintf(
                    'Authentication Time (auth_time) claim indicates that too much time has passed since the last end-user authentication. Current time (%d) is after last auth at %d',
                    $now,
                    $tokenValidUntil
                ));
            }
        }

        return true;
    }
}
