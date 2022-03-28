<?php

declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security;

use Auth0\JWTAuthBundle\Security\Helpers\JwtValidations;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Helpers\JWKFetcher;
use Auth0\SDK\Helpers\Tokens\AsymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\SymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\TokenVerifier;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Psr16Cache;

/**
 * Service that provides access to the Auth0 SDK and JWT validation
 */
class Auth0Service
{


    /**
     * Stores the configured client secret.
     */
    protected string $clientSecret;

    /**
     * Stores the configured API audience.
     */
    protected string $audience;

    /**
     * Stores the configured authorized issuer.
     */
    protected string $issuer;

    /**
     * Stores the configured token algorithm; either RS256 or HS256.
     */
    protected string $algorithm;

    /**
     * A key-value pair representing validations to run on tokens during decoding.
     *
     * @var array<string,mixed>
     */
    protected array $validations;

    /**
     * Instance of a PSR-6 compatible caching interface.
     */
    protected ?CacheItemPoolInterface $configuredCache = null;

    /**
     * Instance of a PSR-16 compatible caching interface.
     */
    protected ?CacheInterface $cache = null;

    /**
     * Stores information about a provided JWT, updated with decodeJWT().
     *
     * @var array<string,mixed>
     */
    protected array $tokenInfo;

    /**
     * Decodes the JWT and validates it. Throws an exception if invalid.
     *
     * @param string                   $token            An encoded JWT token.
     * @param array<string, mixed>|null $claimsToValidate A key => value pair of JWT claims to validate. If null will use defaults configured.
     * @param array<string,mixed>      $options          Options to adjust the verification.
     *                    - "nonce" to check the nonce contained in the token (recommended).
     *                    - "max_age" to check the auth_time of the token.
     *                    - "leeway" clock tolerance in seconds for the current check only. See $leeway above for default.
     *
     * @throws InvalidTokenException Thrown if token fails to validate.
     */
    public function decodeJWT(string $token, ?array $claimsToValidate = null, array $options = []): \stdClass
    {
        $options['nonce'] ?? null;
        $now = $options['now'] ?? time();
        $maxAge = $options['max_age'] ?? null;
        $leeway = $options['leeway'] ?? 60;

        $maxAge = is_numeric($maxAge) ? intval($maxAge) : null;
        $leeway = is_numeric($leeway) ? intval($leeway) : null;
        $now = is_numeric($now) ? intval($now) : null;

        if ($this->algorithm === 'HS256') {
            $signatureVerifier = new SymmetricVerifier($this->clientSecret);
        } else {
            $jwksFetcher = new JWKFetcher($this->getCache(), [ 'base_uri' => $this->issuer.'.well-known/jwks.json' ]);
            $signatureVerifier = new AsymmetricVerifier($jwksFetcher);
        }

        $tokenVerifier = new TokenVerifier($this->issuer, $this->audience, $signatureVerifier);
        $verifiedToken = $tokenVerifier->verify($token, [ 'leeway' => $leeway ]);

        if ($claimsToValidate === null) {
            $claimsToValidate = $this->validations;
        }

        JwtValidations::validateClaims($claimsToValidate, $verifiedToken);
        JwtValidations::validateAge($maxAge, $verifiedToken, $leeway, $now);

        $this->tokenInfo = $verifiedToken;

        return (object) $this->tokenInfo;
    }

    private function getCache(): ?CacheInterface
    {
        if ($this->cache instanceof CacheInterface) {
            return $this->cache;
        }

        if ($this->configuredCache !== null) {
            return $this->cache = new Psr16Cache($this->configuredCache);
        }

        return null;
    }
}
