<?php declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security;

use stdClass;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\Helpers\JWKFetcher;
use Auth0\SDK\Helpers\Tokens\AsymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\SymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\TokenVerifier;
use Auth0\SDK\Exception\InvalidTokenException;
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
     * @param string                 $clientId         Your Auth0 Client ID.
     * @param string                 $clientSecret     Your Auth0 Client secret.
     * @param string                 $audience         Your Auth0 API identifier.
     * @param string                 $authorizedIssuer This will be generated from $domain if not provided.
     * @param string                 $algorithm        Must be either 'RS256' (default) or 'HS256'.
     * @param CacheItemPoolInterface $cache            A PSR-6 or PSR-16 compatible cache interface.
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
     * @param string              $token   An encoded JWT token.
     * @param array<string,mixed> $options Options to adjust the verification.
     *      - "nonce" to check the nonce contained in the token (recommended).
     *      - "max_age" to check the auth_time of the token.
     *      - "leeway" clock tolerance in seconds for the current check only. See $leeway above for default.
     *
     * @return stdClass
     *
     * @throws InvalidTokenException Thrown if token fails to validate.
     */
    public function decodeJWT(string $token, array $options = []): ?stdClass
    {
        $leeway            = $options['leeway'] ?? 60;
        $signatureVerifier = null;
        $tokenInfo         = null;

        if ('HS256' === $this->algorithm) {
            $signatureVerifier = new SymmetricVerifier($this->clientSecret);
        } else {
            $jwksFetcher       = new JWKFetcher($this->cache, [ 'base_uri' => $this->issuer.'.well-known/jwks.json' ]);
            $signatureVerifier = new AsymmetricVerifier($jwksFetcher);
        }

        $tokenVerifier = new TokenVerifier($this->issuer, $this->audience, $signatureVerifier);
        $tokenInfo     = $tokenVerifier->verify($token, [ 'leeway' => $leeway ]);

        if (! empty($options['nonce'])) {
            $tokenNonce = $tokenInfo['nonce'] ?? null;

            if (! $tokenNonce || ! is_string($tokenNonce)) {
                throw new InvalidTokenException('Nonce (nonce) claim must be a string present in the ID token');
            }

            if ($tokenNonce !== $options['nonce']) {
                throw new InvalidTokenException( sprintf(
                    'Nonce (nonce) claim mismatch in the ID token; expected "%s", found "%s"',
                    $options['nonce'],
                    $tokenNonce
                ) );
            }
        }

        if ($this->clientId) {
            $tokenAzp = $tokenInfo['azp'] ?? null;

            if (! $tokenAzp || ! is_string($tokenAzp)) {
                throw new InvalidTokenException(
                    'Authorized Party (azp) claim must be a string present in the ID token when Audience (aud) claim has multiple values'
                );
            }

            if ($tokenAzp !== $this->clientId) {
                throw new InvalidTokenException( sprintf(
                    'Authorized Party (azp) claim mismatch in the ID token; expected "%s", found "%s"',
                    $this->clientId,
                    $tokenAzp
                ) );
            }
        }

        if ($this->audience) {
            $tokenAud = $tokenInfo['aud'] ?? null;

            if (is_array($tokenAud) && count($tokenAud) > 1) {
                if (! in_array($this->audience, $tokenAud)) {
                    throw new InvalidTokenException( sprintf(
                        'Audience (aud) claim missing expected "%s"',
                        $this->audience
                    ) );
                }
            }
        }

        if (! empty($options['max_age'])) {
            $now           = $options['time'] ?? time();
            $tokenAuthTime = $tokenInfo['auth_time'] ?? null;

            if (! $tokenAuthTime || ! is_int($tokenAuthTime)) {
                throw new InvalidTokenException(
                    'Authentication Time (auth_time) claim must be a number present in the ID token when Max Age (max_age) is specified'
                );
            }

            $authValidUntil = $tokenAuthTime + $options['max_age'] + $leeway;

            if ($now > $authValidUntil) {
                throw new InvalidTokenException( sprintf(
                    'Authentication Time (auth_time) claim in the ID token indicates that too much time has passed since the last end-user authentication. Current time (%d) is after last auth at %d',
                    $now,
                    $authValidUntil
                ) );
            }
        }

        $this->tokenInfo = $tokenInfo;
        $this->token     = $token;

        return (object) $this->tokenInfo;
    }
}
