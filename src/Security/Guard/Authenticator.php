<?php

declare(strict_types=1);

namespace Auth0\Symfony\Security\Guard;

use Auth0\Symfony\Contracts\Security\Guard\AuthenticatorInterface;
use Auth0\Symfony\Exceptions\TokenException;
use Auth0\Symfony\Security\Service;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class Authenticator extends AbstractAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        private Service $service
    )
    {
    }

    public function authenticate(Request $request): Passport
    {
        // Extract any available value from the authorization header
        $token = trim($request->headers->get('Authorization', ''));

        // Ensure the 'authorization' header is present in the request
        if ('' === $token) {
            throw TokenException::missingAuthorizationHeader();
        }

        // Ensure the 'authorization' header includes a bearer prefixed JSON web token.
        if (0 !== stripos($token, 'bearer ')) {
            throw TokenException::badAuthorizationHeader();
        }

        // Strip the 'bearer' portion of the authorization string.
        $token = str_ireplace('bearer ', '', $token);

        // Decode, validate and verify token.
        $this->service->getSdk()->decode(
            token: $token,
            tokenType: \Auth0\SDK\Token::TYPE_TOKEN
        );

        return new SelfValidatingPassport(new UserBadge($token));
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') && stripos((string) $request->headers->get('Authorization'), 'Bearer ') === 0;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Let the request continue, we don't want to redirect the user to some login page.
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $response = [
            'message' => 'Authentication failed: ' . strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($response, JsonResponse::HTTP_UNAUTHORIZED);
    }
}
