<?php declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security;

use stdClass;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\Helpers\JWKFetcher;
use Auth0\SDK\Helpers\Tokens\AsymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\SymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\TokenVerifier;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\JWTAuthBundle\Security\Helpers\JwtValidations;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Psr16Cache;

/**
 * Service that provides access to the Auth0 SDK and JWT validation
 */
class Auth0Service
{

    /**
     * Stores an instance of Auth0\SDK\API\Authentication.
     *
     * @var Authentication
     */
    protected $a0;

    /**
     * Stores the configured tenant domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * Stores the configured client id.
     *
     * @var string
     */
    protected $clientId;

    /**
     * Stores the configured client secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * Stores the configured API audience.
     *
     * @var string
     */
    protected $audience;

    /**
     * Stores the configured authorized issuer.
     *
     * @var string
     */
    protected $issuer;

    /**
     * Stores the configured token algorithm; either RS256 or HS256.
     *
     * @var string
     */
    protected $algorithm;

    /**
     * A key-value pair representing validations to run on tokens during decoding.
     *
     * @var array<string,mixed>
     */
    protected $validations;

    /**
     * Instance of a PSR-16 compatible caching interface.
     *
     * @var CacheInterface|null
     */
    protected $cache;

    /**
     * Stores a provided JWT, set during decodeJWT().
     *
     * @var string
     */
    protected $token;

    /**
     * Stores information about a provided JWT, updated with decodeJWT().
     *
     * @var array<string,mixed>
     */
    protected $tokenInfo;

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
    )
    {
        $this->domain       = $domain;
        $this->clientId     = $clientId ?? '';
        $this->clientSecret = $clientSecret ?? '';
        $this->audience     = $audience ?? '';
        $this->issuer       = 'https://'.$this->domain.'/';
        $this->algorithm    = (null !== $algorithm && mb_strtoupper($algorithm) === 'HS256') ? 'HS256' : 'RS256';
        $this->validations  = $validations ?? [];
        $this->cache        = $cache ? new Psr16Cache($cache) : null;

        if (null !== $authorizedIssuer && strlen($authorizedIssuer)) {
            $this->issuer = $authorizedIssuer;
        }

        $this->a0 = new Authentication($this->domain, $this->clientId);
    }

    /**
     * Get the Auth0 User Profile based on the JWT (and validate it).
     *
     * @param string $jwt The encoded JWT token.
     *
     * @return array<string,mixed>
     */
    public function getUserProfileByA0UID(string $jwt): ?array
    {
        // The /userinfo endpoint is only accessible with RS256.
        // Return details from JWT instead, in this case.
        if ('HS256' === $this->algorithm) {
            return (array) $this->tokenInfo;
        }

        return $this->a0->userinfo($jwt);
    }

    /**
     * Decodes the JWT and validates it. Throws an exception if invalid.
     *
     * @param string                   $token            An encoded JWT token.
     * @param null|array<string,mixed> $claimsToValidate A key => value pair of JWT claims to validate. If null will use defaults configured.
     * @param array<string,mixed>      $options          Options to adjust the verification.
     *                    - "nonce" to check the nonce contained in the token (recommended).
     *                    - "max_age" to check the auth_time of the token.
     *                    - "leeway" clock tolerance in seconds for the current check only. See $leeway above for default.
     *
     * @return stdClass
     *
     * @throws InvalidTokenException Thrown if token fails to validate.
     */
    public function decodeJWT(string $token, ?array $claimsToValidate = null, array $options = []): ?stdClass
    {
        $nonce             = $options['nonce'] ?? null;
        $now               = $options['now'] ?? time();
        $maxAge            = $options['max_age'] ?? null;
        $leeway            = $options['leeway'] ?? 60;
        $signatureVerifier = null;
        $verifiedToken     = null;

        if ('HS256' === $this->algorithm) {
            $signatureVerifier = new SymmetricVerifier($this->clientSecret);
        } else {
            $jwksFetcher       = new JWKFetcher($this->cache, [ 'base_uri' => $this->issuer.'.well-known/jwks.json' ]);
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
        $this->token     = $token;

        return (object) $this->tokenInfo;
    }
}
