<?php

declare(strict_types=1);

namespace Auth0\JWTAuthBundle\Security\Guard;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

trait AuthenticatorTrait
{
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') &&
            strpos($request->headers->get('Authorization'), 'Bearer') === 0;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Let the request continue, we don't want to redirect the user to some login page.
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $responseBody = [
            'message' => sprintf(
                'Authentication failed: %s.',
                rtrim($exception->getMessage(), '.')
            ),
        ];

        return new JsonResponse($responseBody, JsonResponse::HTTP_UNAUTHORIZED);
    }
}
