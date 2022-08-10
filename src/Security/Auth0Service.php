<?php

declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security;

use Auth0\JWTAuthBundle\Security\Helpers\JwtValidations;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\Contract\TokenInterface;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Helpers\Tokens\AsymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\SymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\TokenVerifier;
use Auth0\SDK\Token\Verifier;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Psr16Cache;

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;

/**
 * Service that provides access to the Auth0 SDK and JWT validation
 */
class Auth0Service
{
    /**
     * Stores an instance of Auth0\SDK\API\Authentication.
     */
    protected Auth0 $a0;

    /**
     * Stores the configured tenant domain.
     */
    protected string $domain;

    /**
     * Stores the configured client id.
     */
    protected string $clientId;

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
    protected ?TokenInterface $tokenInfo;

    /**
     * Stores a provided JWT, set during decodeJWT().
     */
    protected ?string $token;

    /**
     * Auth0Service constructor.
     *
     * @param string                 $domain           Required. Auth0 domain for your tenant.
     * @param string                 $clientId         Your Auth0 Client ID.
     * @param string                 $clientSecret     Your Auth0 Client secret.
     * @param string                 $audience         Your Auth0 API identifier.
     * @param string                 $authorizedIssuer This will be generated from $domain if not provided.
     * @param string                 $algorithm        Must be either 'RS256' (default) or 'HS256'.
     * @param array<string,mixed>    $validations      A key-value pair representing validations to run on tokens during decoding.
     * @param CacheItemPoolInterface $cache            A PSR-6 or PSR-16 compatible cache interface.
     */
    public function __construct(
        string $domain,
        ?string $clientId = '',
        ?string $clientSecret = '',
        ?string $audience = '',
        ?string $authorizedIssuer = '',
        ?string $algorithm = 'RS256',
        ?array $validations = [],
        ?CacheItemPoolInterface $cache = null
    ) {
        $this->domain = $domain;
        $this->clientId = $clientId ?? '';
        $this->clientSecret = $clientSecret ?? '';
        $this->audience = $audience ?? '';
        $this->issuer = 'https://'.$this->domain.'/';
        $this->algorithm = $algorithm !== null && mb_strtoupper($algorithm) === 'HS256' ? 'HS256' : 'RS256';
        $this->validations = $validations ?? [];
        $this->configuredCache = $cache;
        $this->token = null;
        $this->tokenInfo = null;

        if ($authorizedIssuer !== null && strlen($authorizedIssuer) !== 0) {
            $this->issuer = $authorizedIssuer;
        }

        $configuration = new SdkConfiguration(
            domain: $this->domain,
            clientId: $this->clientId,
            clientSecret: $this->clientSecret,
            tokenAlgorithm: 'RS256',
            tokenJwksUri: $this->issuer.'.well-known/jwks.json'
        );
        $this->a0 = new Auth0($configuration);
    }
    /**
     * Get the Auth0 User Profile based on the JWT (and validate it).
     *
     * @param string $jwt The encoded JWT token.
     *
     * @return array<mixed>|null
     */
    public function getUserProfileByA0UID(string $jwt): ?array
    {
        // The /userinfo endpoint is only accessible with RS256.
        // Return details from JWT instead, in this case.
        if ($this->algorithm === 'HS256') {
            return $this->tokenInfo;
        }
        return $this->a0->userinfo($jwt);
    }

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
    public function decodeJWT(string $token, ?array $claimsToValidate = null, array $options = []): ?TokenInterface
    {
        $token = trim($token);

        if (strlen($token) === 0) {
            throw new InvalidTokenException();
        }

        $options['nonce'] ?? null;
        $now = $options['now'] ?? time();
        $maxAge = $options['max_age'] ?? null;
        $leeway = $options['leeway'] ?? 60;

        $maxAge = is_numeric($maxAge) ? intval($maxAge) : null;
        $leeway = is_numeric($leeway) ? intval($leeway) : null;
        $now = is_numeric($now) ? intval($now) : null;

        $verifiedToken = $this->a0->decode($token, [$this->audience]);

        if ($claimsToValidate === null) {
            $claimsToValidate = $this->validations;
        }

        JwtValidations::validateClaims($claimsToValidate, $verifiedToken);
        JwtValidations::validateAge($maxAge, $verifiedToken, $leeway, $now);

        $this->tokenInfo = $verifiedToken;
        $this->token = $token;

        return $this->tokenInfo;
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

