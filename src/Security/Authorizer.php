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
     * @param array<mixed>    $configuration
     * @param Service         $service
     * @param LoggerInterface $logger
     */
    public function __construct(
        private array $configuration,
        private Service $service,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @psalm-suppress InternalMethod
     *
     * @param Request $request
     */
    public function authenticate(Request $request): Passport
    {
        $header = trim($request->headers->get(key: 'Authorization', default: '') ?? '');

        if ('' === $header || 0 !== stripos($header, 'bearer ')) {
            throw new AuthenticationException(message: '`Authorization` header is missing or malformed.');
        }

        $token = substr($header, 7);

        $token = $this->getService()->getSdk()->decode(
            token: $token,
            tokenType: \Auth0\SDK\Token::TYPE_ACCESS_TOKEN,
        );

        $user = json_encode(['type' => 'stateless', 'data' => ['user' => $token->toArray()]], JSON_THROW_ON_ERROR);

        return new SelfValidatingPassport(userBadge: new UserBadge(userIdentifier: $user));
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

        return new JsonResponse(data: $response, status: JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * @psalm-suppress InternalMethod
     *
     * @param Request $request
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has(key: 'Authorization') && 0 === stripos((string) $request->headers->get(key: 'Authorization'), 'Bearer ');
    }
}
