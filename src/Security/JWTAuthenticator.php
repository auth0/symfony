<?php


namespace Auth0\JWTAuthBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

use Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface;
use Auth0\SDK\Exception\InvalidTokenException;

class JWTAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    use ContainerAwareTrait;

    /**
     * @var Auth0Service
     */
    protected $auth0Service;

    /**
     * JWTAuthenticator constructor
     *
     * @param Auth0Service $auth0Service Required. An instance of the Auth0Service class.
     */
    public function __construct(Auth0Service $auth0Service)
    {
        $this->auth0Service = $auth0Service;
    }

    /**
     * Generate a pre-authenticated token.
     *
     * @param Request $request
     * @param string  $providerKey
     *
     * @return PreAuthenticatedToken
     *
     * @throws BadCredentialsException
     * @throws InvalidTokenException
     */
    public function createToken(Request $request, $providerKey)
    {
        // Look for an authorization header
        $authorizationHeader = $request->headers->get('Authorization');

        if ($authorizationHeader === null) {
            return new PreAuthenticatedToken(
                'anon.',
                null,
                $providerKey
            );
        }

        // Extract the JWT
        $authToken = str_replace('Bearer ', '', $authorizationHeader);

        // Decode and validate the JWT
        try {
            $token = $this->auth0Service->decodeJWT($authToken);

            if (null !== $token) {
                $token->token = $authToken;
            }
        } catch (\UnexpectedValueException $ex) {
            throw new BadCredentialsException('Invalid token');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $token,
            $providerKey
        );
    }

    /**
     * @param TokenInterface           $token
     * @param JWTUserProviderInterface $userProvider
     * @param string                   $providerKey
     *
     * @return PreAuthenticatedToken
     *
     * @throws AuthenticationException
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        // The user provider should implement JWTUserProviderInterface
        if (! $userProvider instanceof JWTUserProviderInterface) {
            throw new \InvalidArgumentException('Argument must implement interface Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface');
        }

        if (null === $token->getCredentials()) {
            $user = $userProvider->getAnonymousUser();

            return new PreAuthenticatedToken(
                '',
                $token,
                $providerKey,
                []
            );
        }

        $user = $userProvider->loadUserByJWT($token->getCredentials());

        return new PreAuthenticatedToken(
            $user,
            $token,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return boolean
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response("Authentication Failed: {$exception->getMessage()}", 403);
    }

}
