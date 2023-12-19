<?php

declare(strict_types=1);

namespace Auth0\Symfony\Security;

use Auth0\Symfony\Contracts\Security\AuthorizerInterface;
use Auth0\Symfony\Service;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\{Passport, SelfValidatingPassport};

final class Authorizer extends AbstractAuthenticator implements AuthorizerInterface
{
    /**
     * @param array<mixed> $configuration
     * @param Service $service
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function __construct(
        private array $configuration,
        private Service $service,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @psalm-suppress InternalMethod
     */
    public function authenticate(Request $request): Passport
    {
        // Extract any available value from the authorization header
        $param = $request->get('token', null);
        $header = trim($request->headers->get('Authorization', '') ?? '');
        $token = $param ?? $header;
        $usingHeader = null === $param;

        // Ensure the 'authorization' header is present in the request
        if (! is_string($token) || '' === $token) {
            throw new AuthenticationException('`Authorization` header not present.');
        }

        // Ensure the 'authorization' header includes a bearer prefixed JSON web token.
        if ($usingHeader && 0 !== stripos($token, 'bearer ')) {
            throw new AuthenticationException('`Authorization` header is malformed.');
        }

        // Strip the 'bearer' portion of the authorization string.
        $token = str_ireplace('bearer ', '', $token);

        // Decode, validate and verify token.
        $token = $this->getService()->getSdk()->decode(
            token: $token,
            tokenType: \Auth0\SDK\Token::TYPE_ACCESS_TOKEN,
        );

        $user = json_encode(['type' => 'stateless', 'data' => ['user' => $token->toArray()]], JSON_THROW_ON_ERROR);

        return new SelfValidatingPassport(new UserBadge($user));
    }

    /**
     * @return array<mixed>
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $response = [
            'errors' => [
                (object) [
                    'status' => JsonResponse::HTTP_UNAUTHORIZED,
                    'title' => 'Authorization failed',
                    'detail' => strtr($exception->getMessageKey(), $exception->getMessageData()),
                ],
            ],
        ];

        return new JsonResponse($response, JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * @psalm-suppress InternalMethod
     */
    public function supports(Request $request): ?bool
    {
        if (null !== $request->get('token')) {
            return true;
        }

        return $request->headers->has('Authorization') && 0 === stripos((string) $request->headers->get('Authorization'), 'Bearer ');
    }
}
