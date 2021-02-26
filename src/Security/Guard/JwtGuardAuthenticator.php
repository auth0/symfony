<?php

namespace Auth0\JWTAuthBundle\Security\Guard;

use Auth0\JWTAuthBundle\Security\Auth0Service;
use Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface;
use Auth0\SDK\Exception\CoreException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Handles authentication with JSON Web Tokens through the 'Authorization' request header.
 */
class JwtGuardAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * @var Auth0Service
     */
    private $auth0Service;

    /**
     * Constructs a new JwtGuardAuthenticator instance.
     *
     * @param Auth0Service $auth0Service
     */
    public function __construct(Auth0Service $auth0Service)
    {
        $this->auth0Service = $auth0Service;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        return true;
    }

    /**
     * Retrieves the authentication credentials from the 'Authorization' request header.
     *
     * @param Request $request
     *
     * @return array<string,mixed>
     */
    public function getCredentials(Request $request): ?array
    {
        // Removes the 'Bearer ' part from the Authorization header value.
        $jwt = str_replace('Bearer ', '', $request->headers->get('Authorization', ''));

        return [
            'jwt' => $jwt,
        ];
    }

    /**
     * Returns a user based on the information inside the JSON Web Token depending on the implementation
     * of the configured user provider.
     *
     * When the user provider does not implement the JWTUserProviderInterface it will attempt to load
     * the user by username with the 'sub' (subject) claim of the JSON Web Token.
     *
     * @param array<string,mixed>   $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        if ($credentials && isset($credentials['jwt']) && ! empty($credentials['jwt'])) {
            if ($jwt = $this->auth0Service->decodeJWT($credentials['jwt'])) {
                if (! isset($jwt->token)) {
                    $jwt->token = $credentials['jwt'];
                }

                if ($userProvider instanceof JWTUserProviderInterface) {
                    return $userProvider->loadUserByJWT($jwt);
                }

                return $userProvider->loadUserByUsername($jwt->sub);
            }
        }

        // Skip JWT verification exceptions here.
        // Verification will be done in checkCredentials().
        // if ($userProvider instanceof JWTUserProviderInterface) {
        // return $userProvider->getAnonymousUser();
        // }
        return new User('unknown', null, ['IS_AUTHENTICATED_ANONYMOUSLY']);
    }

    /**
     * Returns true when the provided JSON Web Token successfully decodes and validates.
     *
     * @param array<string,mixed> $credentials
     * @param UserInterface       $user
     *
     * @return boolean
     *
     * @throws AuthenticationException when decoding and/or validation of the JSON Web Token fails
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        try {
            if ($credentials && isset($credentials['jwt'])) {
                if (! empty($credentials['jwt'])) {
                    $this->auth0Service->decodeJWT($credentials['jwt']);
                }
            }

            return true;
        } catch (CoreException $exception) {
            throw new AuthenticationException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Returns nothing to continue the request when authenticated.
     *
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // Continue with request.
        return null;
    }

    /**
     * Returns the 'Authentication failed' response.
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $responseBody = [
            'message' => sprintf(
                'Authentication failed: %s.',
                rtrim($exception->getMessage(), '.')
            ),
        ];

        return new JsonResponse($responseBody, JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Returns a response that directs the user to authenticate.
     *
     * @param Request                 $request
     * @param AuthenticationException $authException
     *
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $responseBody = [
            'message' => 'Authentication required.',
        ];

        return new JsonResponse($responseBody, JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
