<?php

namespace Auth0\Symfony\Controllers;

use Auth0\SDK\Auth0;
use Auth0\Symfony\Contracts\Exceptions\AuthenticationControllerInterface;
use Auth0\Symfony\Security\Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class AuthenticationController implements AuthenticationControllerInterface
{
    public function __construct(
        private Authenticator $authenticator,
        private RouterInterface $router,
    )
    {
    }

    public function login(Request $request): Response
    {
        $host = $request->getSchemeAndHttpHost();
        $route = $this->getRedirectUrl('callback');
        $url = $this->getSdk()->login($host . $route);

        return new RedirectResponse($url);
    }

    public function logout(Request $request): Response
    {
        $host = $request->getSchemeAndHttpHost();
        $route = $this->getRedirectUrl('logout');
        $url = $this->getSdk()->logout($host . $route);

        return new RedirectResponse($url);
    }

    public function callback(Request $request): Response
    {
        $code = $request->get('code');
        $state = $request->get('state');

        if (null !== $code && null !== $state) {
            $host = $request->getSchemeAndHttpHost();
            $route = $this->getRedirectUrl('success');
            $redirect = $host . $route;

            try {
                $this->getSdk()->exchange($host . $route, $code, $state);

                if ($request->hasSession()) {
                    $redirect = $request->getSession()->get('auth0:callback_redirect', $redirect);
                    $request->getSession()->remove('auth0:callback_redirect');
                }
            } catch (\Throwable $th) {
                $this->addFlash('error', $th->getMessage());

                $host = $request->getSchemeAndHttpHost();
                $route = $this->getRedirectUrl('failure');
                $redirect = $host . $route;
            }
        }

        return new RedirectResponse($redirect);
    }

    private function getRedirectUrl(string $route): string
    {
        $configuration = $this->authenticator->getConfiguration();
        $routes = $configuration['routes'] ?? [];
        $route = $routes[$route] ?? null;

        if (null !== $route && '' !== $route) {
            try {
                return $this->router->generate($route);
            } catch (\Throwable $th) {
            }
        }

        return '';
    }

    private function getSdk(): Auth0
    {
        return $this->authenticator->getService()->getSdk();
    }
}
