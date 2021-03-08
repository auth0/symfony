<?php declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security;

use stdClass;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\Helpers\JWKFetcher;
use Auth0\SDK\Helpers\Tokens\AsymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\SymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\TokenVerifier;
use Auth0\JWTAuthBundle\Security\Helpers\Auth0Psr16Adapter;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

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
     * @param string                 $clientId         Optional. Your Auth0 Client ID.
     * @param string                 $clientSecret     Optional. Your Auth0 Client secret.
     * @param string                 $audience         Optional. Your Auth0 API identifier.
     * @param string                 $authorizedIssuer Optional. This will be generated from $domain if not provided.
     * @param string                 $algorithm        Optional. Must be either 'RS256' (default) or 'HS256'.
     * @param CacheItemPoolInterface $cache            Optional. A PSR-6 or PSR-16 compatible cache interface.
     */
    public function __construct(
        string $domain,
        ?string $clientId = '',
        ?string $clientSecret = '',
        ?string $audience = '',
        ?string $authorizedIssuer = '',
        ?string $algorithm = 'RS256',
        ?CacheItemPoolInterface $cache = null
    )
    {
        $this->domain       = $domain;
        $this->clientId     = $clientId ?? '';
        $this->clientSecret = $clientSecret ?? '';
        $this->audience     = $audience ?? '';
        $this->issuer       = 'https://'.$this->domain.'/';
        $this->algorithm    = (null !== $algorithm && mb_strtoupper($algorithm) === 'HS256') ? 'HS256' : 'RS256';
        $this->cache        = $cache ? new Auth0Psr16Adapter($cache) : null;

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
     * Decodes the JWT and validate it.
     *
     * @param string $token An encoded JWT token.
     *
     * @return stdClass
     */
    public function decodeJWT(string $token): ?stdClass
    {
        $jwksUri     = $this->issuer.'.well-known/jwks.json';
        $sigVerifier = null;

        if ('HS256' === $this->algorithm) {
            $sigVerifier = new SymmetricVerifier($this->clientSecret);
        } else {
            $jwksFetcher = new JWKFetcher($this->cache, [ 'base_uri' => $jwksUri ]);
            $sigVerifier = new AsymmetricVerifier($jwksFetcher);
        }

        $tokenVerifier = new TokenVerifier($this->issuer, $this->audience, $sigVerifier);

        $this->tokenInfo = $tokenVerifier->verify($token);
        $this->token     = $token;

        return (object) $this->tokenInfo;
    }
}
