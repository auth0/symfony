<?php

namespace Auth0\JWTAuthBundle\Security;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Helpers\JWKFetcher;
use Auth0\SDK\Helpers\Tokens\AsymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\TokenVerifier;

/**
 * Service that provides access to the Auth0 SDK and JWT validation
 */
class Auth0Service
{

    /**
     * @var Authentication
     */
    protected $a0;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $audience;

    /**
     * @var string
     */
    protected $issuer;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var array<string,mixed>
     */
    protected $tokenInfo;

    /**
     * Auth0Service constructor.
     *
     * @param string $domain            Required. Auth0 domain for your tenant.
     * @param string $clientId          Required. Your Auth0 Client ID.
     * @param string $audience          Required. Your Auth0 API identifier.
     * @param string $authorized_issuer Optional. This will be generated from $domain if not provided.
     */
    public function __construct(string $domain, string $clientId, string $audience, ?string $authorized_issuer)
    {
        $this->issuer = strlen($authorized_issuer) ? $authorized_issuer : "https://{$domain}/";

        $this->domain   = $domain;
        $this->clientId = $clientId;
        $this->audience = $audience;

        $this->a0 = new Authentication($domain, $clientId);
    }

    /**
     * Get the Auth0 User Profile based on the JWT (and validate it).
     *
     * @param string $jwt The encoded JWT token
     *
     * @return array<string,mixed>
     */
    public function getUserProfileByA0UID(string $jwt): ?array
    {
        return $this->a0->userinfo($jwt);
    }

    /**
     * Decodes the JWT and validate it
     *
     * @return \stdClass
     */
    public function decodeJWT(string $token): ?\stdClass
    {
        $jwksUri = $this->issuer.'.well-known/jwks.json';

        $jwksFetcher   = new JWKFetcher(null, [ 'base_uri' => $jwksUri ]);
        $sigVerifier   = new AsymmetricVerifier($jwksFetcher);
        $tokenVerifier = new TokenVerifier($this->issuer, $this->audience, $sigVerifier);

        $this->tokenInfo = $tokenVerifier->verify($token);
        $this->token     = $token;

        return (object) $this->tokenInfo;
    }
}
