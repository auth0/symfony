<?php
/**
 * Created by PhpStorm.
 * User: german
 * Date: 1/22/15
 * Time: 9:30 PM
 */

namespace AppBundle\Security;


use Auth0\JWTAuthBundle\Security\Auth0Service;
use Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Class A0UserProvider
 * This class should be the responsible of translate the JWT
 * to your apps User model.
 *
 * @package AppBundle\Security
 */
class A0UserProvider implements JWTUserProviderInterface
{
    protected $auth0Service;

    /**
     * The Auth0Service is injected in order to communicate with the
     * Auth0 API. This will probably not needed on your app
     * @param Auth0Service $auth0Service
     */
    public function __construct(Auth0Service $auth0Service) {
        $this->auth0Service = $auth0Service;
    }

    /**
     * Since your provider should implement the JWTUserProviderInterface,
     * you should implement this method.
     *
     * This method will have the logic to retrieve the necessary info to build
     * your user object and return to the SecurityProvider.
     *
     * In this case, it is calling the Auth0 API in order to get the user data,
     * this is intended just for this demo, it probably wont be a good practice
     * because of the latency it involves.
     *
     * You will probably search in your database to retrieve the user related
     * to the token received (you also can make Auth0 to populate the token
     * with extra info).
     *
     * @param string $jwt This is the decoded JWT (it is overloaded with the token property with is the token receiven on the request headers
     * @return \Auth0\JWTAuthBundle\Security\Core\UserInterface
     */
    public function loadUserByJWT($jwt) {
        $data = $this->auth0Service->getUserProfileByJWT($jwt->token);

        return new A0User($data, array('ROLE_OAUTH_USER'));
    }

    public function loadUserByUsername($username)
    {
        throw new NotImplementedException('method not implemented');
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'AppBundle\Security\A0User';
    }
}