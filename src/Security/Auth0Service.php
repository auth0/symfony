<?php
/**
 * Created by PhpStorm.
 * User: german
 * Date: 1/20/15
 * Time: 11:54 PM
 */

namespace Auth0\JWTAuthBundle\Security;

use Auth0\SDK\Auth0JWT;
use Auth0\SDK\API\ApiUsers;
use Symfony\Component\Security\Core\User\User;

/**
 * Service that provides access to the Auth0 SDK and JWT validation
 */
class Auth0Service {

    private $client_id;
    private $client_secret;
    private $oauth_client;

    public function __construct($client_id, $client_secret){
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }

    /**
     * Get the Auth0 User Profile based on the JWT (and validate it).
     * @return User info as described in https://docs.auth0.com/user-profile
     */
    public function getUserProfileByA0UID($jwt, $a0UID) {
        return ApiUsers::get($jwt, $a0UID);
    }

    /**
     * Decodes the JWT and validate it
     * @return stdClass
     */
    public function decodeJWT($encToken) {

        return Auth0JWT::decode($encToken, $this->client_id, $this->client_secret);

    }
}
