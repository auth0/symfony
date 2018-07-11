<?php

namespace Auth0\JWTAuthBundle\Security;

use Auth0\SDK\Helpers\Cache\CacheHandler;
use Auth0\SDK\JWTVerifier;
use Auth0\SDK\Auth0Api;
use Auth0\SDK\API\Authentication;
use Symfony\Component\Security\Core\User\User;

/**
 * @author german
 *
 * Service that provides access to the Auth0 SDK and JWT validation
 */
class Auth0Service {

    private $api_secret;
    private $domain;
    private $api_identifier;
    private $authorized_issuer;
    private $secret_base64_encoded;
    private $supported_algs;
    private $authApi;

    /**
     * @var CacheHandler|null
     */
    private $cache;

    /**
     * Auth0Service constructor.
     *
     * @param string $api_secret
     * @param string $domain
     * @param array|string $api_identifier
     * @param string $authorized_issuer
     * @param boolean $secret_base64_encoded
     * @param array $supported_algs
     * @param CacheHandler|null $cache
     */
    public function __construct($api_secret, $domain, $api_identifier, $authorized_issuer, $secret_base64_encoded, $supported_algs, CacheHandler $cache = null)
    {
        $this->api_secret = $api_secret;
        $this->domain = $domain;
        $this->api_identifier = $api_identifier;
        $this->authorized_issuer = $authorized_issuer;
        $this->secret_base64_encoded = $secret_base64_encoded;
        $this->supported_algs = $supported_algs;
        $this->cache = $cache;

        $this->authApi = new Authentication($this->domain);
    }

    /**
     * Get the Auth0 User Profile based on the JWT (and validate it).
     *
     * @return User info as described in https://docs.auth0.com/user-profile
     */
    public function getUserProfileByA0UID($jwt, $a0UID)
    {
        return $this->authApi->userinfo($jwt);
    }

    /**
     * Decodes the JWT and validate it
     *
     * @return \stdClass
     */
    public function decodeJWT($encToken)
    {
        $config = [
            // The api_identifier setting could come through as an array or a string.
            'valid_audiences' => is_array($this->api_identifier) ? $this->api_identifier : [$this->api_identifier],
            'client_secret' => $this->api_secret,
            'authorized_iss' => [$this->authorized_issuer],
            'supported_algs' => $this->supported_algs,
            'secret_base64_encoded' => $this->secret_base64_encoded
        ];

        if (null !== $this->cache) {
            $config['cache'] = $this->cache;
        }

        $verifier = new JWTVerifier($config);

        return $verifier->verifyAndDecode($encToken);
    }
}
