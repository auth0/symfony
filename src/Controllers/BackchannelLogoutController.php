<?php

declare(strict_types=1);

namespace Auth0\Symfony\Controllers;

use Auth0\SDK\Auth0;
use Auth0\Symfony\Contracts\Controllers\AuthenticationControllerInterface;
use Auth0\Symfony\Security\Authenticator;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Throwable;

use function is_string;

final class BackchannelLogoutController extends AbstractController implements AuthenticationControllerInterface
{
    public function __construct(
        private Authenticator $authenticator,
        protected ContainerInterface $container,
    ) {
    }

    /**
     * @psalm-suppress InternalMethod
     */
    public function handle(Request $request): Response
    {
        if ('POST' !== $request->getMethod()) {
            return new Response('', Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $logoutToken = $request->get('logout_token');

        if (! is_string($logoutToken)) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        $logoutToken = trim($logoutToken);

        if ('' === $logoutToken) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->getSdk()->handleBackchannelLogout($logoutToken);
        } catch (Throwable $throwable) {
            return new Response($throwable->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response('', Response::HTTP_OK);
    }

    private function getSdk(): Auth0
    {
        return $this->authenticator->service->getSdk();
    }
}
