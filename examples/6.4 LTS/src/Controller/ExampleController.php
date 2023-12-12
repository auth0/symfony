<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExampleController extends AbstractController
{
    public function public(): Response
    {
        return new Response(
            '<html>
                <body>
                    <p>Lucky number: ' . random_int(0, 100) . '</p>
                    <p><a href="/login">Login</a></p>
                </body>
            </html>'
        );
    }

    /*
     * [AUTH0/SYMFONY] This demonstrates a route that requires authentication.
     */
    public function private(): Response
    {
        return new Response(
            '<html>
                <body>
                    <p><pre>' . print_r($this->getUser(), true) . '</pre></p>
                    <p><a href="/logout">Logout</a></p>
                </body>
            </html>'
        );
    }

    /*
     * [AUTH0/SYMFONY] This demonstrates an unprotected API route.
     */
    public function apiPublic(): Response
    {
        return new JsonResponse([
            'message' => 'Public API'
        ]);
    }

    /*
     * [AUTH0/SYMFONY] This demonstrates a protected API route; it requires a valid token to access. Allowed scopes are defined in `config/packages/security.yaml` file. In this case, the route is protected by the `ROLE_USING_TOKEN` role, which simply means it requires a valid token.
     */
    public function apiPrivate(): Response
    {
        return new JsonResponse([
            'message' => 'Private API',
            'roles' => $this->getUser()->getRoles()
        ]);
    }

    /*
     * [AUTH0/SYMFONY] This demonstrates a protected API route; it requires a valid token with the "read:messages" scope to access. Allowed scopes are defined in `config/packages/security.yaml` file.
     */
    public function apiPrivateScopes(): Response
    {
        return new JsonResponse([
            'message' => 'Private API with scope!',
            'roles' => $this->getUser()->getRoles()
        ]);
    }
}
