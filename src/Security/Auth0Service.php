<?php

namespace Auth0\JWTAuthBundle\Security;

use Auth0\SDK\JWTVerifier;
use Auth0\SDK\Auth0Api;
use Auth0\SDK\API\Management;
use Symfony\Component\Security\Core\User\User;

/**
 * @author german
 *
 * Service that provides access to the Auth0 SDK and JWT validation
 */
class Auth0Service {

    private $client_id;
    private $client_secret;
    private $domain;
    private $api_identifier;
    private $authorized_issuer;
    private $secret_base64_encoded;
    private $supported_algs;

    /**
     * @param string $client_id
     * @param string $client_secret
     * @param string $domain
     */
    public function __construct($client_id, $client_secret, $domain, $api_identifier, $authorized_issuer, $secret_base64_encoded, $supported_algs)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->domain = $domain;
        $this->api_identifier = $api_identifier;
        $this->authorized_issuer = $authorized_issuer;
        $this->secret_base64_encoded = $secret_base64_encoded;
        $this->supported_algs = $supported_algs;
    }

    /**
     * Get the Auth0 User Profile based on the JWT (and validate it).
     *
     * @return User info as described in https://docs.auth0.com/user-profile
     */
    public function getUserProfileByA0UID($jwt, $a0UID)
    {
        $auth0Api = new Management($jwt, $this->domain);
        return $auth0Api->users->get($a0UID);
    }

    /**
     * Decodes the JWT and validate it
     *
     * @return \stdClass
     */
    public function decodeJWT($encToken)
    {
        $verifier = new JWTVerifier([
            'valid_audiences' => [ $this->client_id, $this->api_identifier ],
            'client_secret' => $this->client_secret,
            'authorized_iss' => $this->authorized_issuer,
            'supported_algs' => $this->supported_algs,
            'secret_base64_encoded' => $this->secret_base64_encoded
        ]);

        return $verifier->verifyAndDecode($encToken);
    }
}
