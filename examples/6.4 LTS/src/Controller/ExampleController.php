<?php

namespace App\Controller;

use Auth0\Symfony\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExampleController extends AbstractController
{
    public function __construct(
        // [AUTH0/SYMFONY] The bundle registers its service by the id `auth0`, so we autowire it by id.
        #[Autowire(service: 'auth0')]
        private Service $auth0,
    ) {
    }

    public function index(): Response
    {
        $session = $this->auth0->getSdk()->getCredentials();

        return $this->render('index.html.twig', [
            'session' => $session,
        ]);
    }

    /*
     * [AUTH0/SYMFONY] This demonstrates a route that requires authentication.
     */
    public function private(): Response
    {
        return $this->render('private.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /*
     * [AUTH0/SYMFONY] This demonstrates an unprotected API route.
     */
    public function apiPublic(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'This is a public API endpoint.',
        ]);
    }

    /*
     * [AUTH0/SYMFONY] This demonstrates a protected API route; it requires a valid token to access. The `ROLE_USING_TOKEN` role (added by the SDK to any request with a valid token) is enforced in `config/packages/security.yaml`.
     */
    public function apiPrivate(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'This is a private API endpoint.',
            'roles' => $this->getUser()->getRoles(),
        ]);
    }

    /*
     * [AUTH0/SYMFONY] This demonstrates a protected API route; it requires a valid token with the `read:messages` scope. Allowed scopes are defined in `config/packages/security.yaml`.
     */
    public function apiPrivateScoped(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'This is a scoped API endpoint (read:messages).',
            'roles' => $this->getUser()->getRoles(),
        ]);
    }
}
