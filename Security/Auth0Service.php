<?php
/**
 * Created by PhpStorm.
 * User: german
 * Date: 1/20/15
 * Time: 11:54 PM
 */

namespace Auth0\JWTAuthBundle\Security;

use Auth0SDK\Auth0;
use Symfony\Component\Security\Core\User\User;

/**
 * Service that provides access to the Auth0 SDK and JWT validation
 */
class Auth0Service {

    private $client_id;
    private $client_secret;
    private $domain;
    private $oauth_client;

    public function __construct($client_id, $client_secret, $domain, $redirect_uri){
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->domain = $domain;
    }

    /**
     * Builds and return the OAuth client
     */
    protected function getOAuthClient() {
        if (is_null($this->oauth_client)) {
            $this->oauth_client = new \OAuth2\Client($this->client_id, $this->client_secret);

            $auth_url = "https://{$this->domain}/oauth/token/";
            $auth0_response = $this->oauth_client->getAccessToken($auth_url, "client_credentials", array());

            $this->oauth_client->setAccessToken($auth0_response['result']['access_token']);
            $this->oauth_client->setAccessTokenType(\OAuth2\Client::ACCESS_TOKEN_BEARER);
        }
        return $this->oauth_client;
    }

    /**
     * Get the Auth0 User Profile based on the Auth0 user id
     * @return User info as described in https://docs.auth0.com/user-profile
     */
    public function getUserProfileByA0UID($a0UID) {
        $oAuthClient = $this->getOAuthClient();

        $url = "https://{$this->domain}/api/users/{$a0UID}";
        $user_info = (object)$oAuthClient->fetch($url);

        return (object)$user_info['result'];
    }

    /**
     * Get the Auth0 User Profile based on the JWT (and validate it).
     * @return User info as described in https://docs.auth0.com/user-profile
     */
    public function getUserProfileByJWT($jwt) {
        $oAuthClient = $this->getOAuthClient();

        $url = "https://{$this->domain}/tokeninfo/";
        $user_info = $oAuthClient->fetch($url, array('id_token' => $jwt));

        return (object)$user_info['result'];
    }

    /**
     * Decodes the JWT and validate it
     * @return stdClass
     */
    public function decodeJWT($encToken) {
        // Decode the user
        $token = \JWT::decode($encToken, base64_decode(strtr($this->client_secret, '-_', '+/')) );

        // validate that this JWT was made for us
        if ($token->aud != $this->client_id) {
            throw new \UnexpectedValueException();
        }

        $token->token = $encToken;

        return $token;
    }
}