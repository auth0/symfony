<?php

declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security;

use Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

/**
 * A SimplePreAuthenticator interface for securing your Symfony application.
 *
 * @deprecated As of Symfony 4.2, you should switch to using JwtGuardAuthenticator instead.
 *
 * @package Auth0\JWTAuthBundle\Security
 */
class JWTAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    use ContainerAwareTrait;

    /**
     * Reference to an instance of Auth0Service.
     */
    protected Auth0Service $auth0Service;

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
     * @param Request $request     Symfony representation of the HTTP request message.
     * @param string  $providerKey String representation of the provider.
     *
     * @throws BadCredentialsException When an invalid token is provided.
     */
    public function createToken(Request $request, string $providerKey): PreAuthenticatedToken
    {
        // Look for an authorization header.
        $authorizationHeader = $request->headers->get('Authorization');

        if ($authorizationHeader === null) {
            return new PreAuthenticatedToken(
                'anon.',
                null,
                $providerKey
            );
        }

        // Extract the JWT.
        $authToken = str_replace('Bearer ', '', $authorizationHeader);

        // Decode and validate the JWT.
        try {
            $token = $this->auth0Service->decodeJWT($authToken);

            if ($token !== null) {
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
     * Authenticate a provided token.
     *
     * @param TokenInterface           $token        Symfony authentication token.
     * @param JWTUserProviderInterface $userProvider A UserProviderInterface instance.
     * @param string                   $providerKey  String representation of the provider.
     *
     * @throws InvalidArgumentException When an invalid provider interface is passed.
     * @throws AuthenticationException  When an authentication failure occurs.
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, string $providerKey): PreAuthenticatedToken
    {
        // The user provider should implement JWTUserProviderInterface.
        if (! $userProvider instanceof JWTUserProviderInterface) {
            throw new InvalidArgumentException(
                'Argument must implement interface Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface'
            );
        }

        if ($token->getCredentials() === null) {
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
            array_map(static function ($role) {
                return (string) $role;
            }, $user->getRoles())
        );
    }

    /**
     * Check if $token is compatible and provider keys match before handing off to authenticateToken().
     *
     * @param TokenInterface $token       Symfony authentication token.
     * @param string         $providerKey String representation of the provider.
     */
    public function supportsToken(TokenInterface $token, string $providerKey): bool
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * Event raised when an authentication error occurs.
     *
     * @param Request                 $request   Symfony representation of the HTTP request message.
     * @param AuthenticationException $exception A object representing the error.
     */
    // phpcs:ignore
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new Response('Authentication Failed: '.$exception->getMessage(), 403);
    }
}
