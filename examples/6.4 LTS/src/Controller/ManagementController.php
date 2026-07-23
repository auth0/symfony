<?php

namespace App\Controller;

use Auth0\SDK\API\Management\Clients\Requests\ListClientsRequestParameters;
use Auth0\SDK\API\Management\Connections\Requests\ListConnectionsQueryParameters;
use Auth0\SDK\API\Management\Organizations\Requests\ListOrganizationsRequestParameters;
use Auth0\SDK\API\Management\ResourceServers\Requests\ListResourceServerRequestParameters;
use Auth0\SDK\API\Management\Roles\Requests\ListRolesRequestParameters;
use Auth0\SDK\API\Management\Users\Requests\ListUsersRequestParameters;
use Auth0\Symfony\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagementController extends AbstractController
{
    private const PAGE_SIZE = 25;

    /**
     * Read-only Management endpoints to iterate through. Each key is a v9
     * sub-client property (exposed via the Management client); the value pairs
     * a display label with the request-parameters class for that endpoint's
     * list() method.
     *
     * @var array<string, array{label: string, request: class-string}>
     */
    private const RESOURCES = [
        'users' => ['label' => 'List Users', 'request' => ListUsersRequestParameters::class],
        'clients' => ['label' => 'List Clients', 'request' => ListClientsRequestParameters::class],
        'connections' => ['label' => 'List Connections', 'request' => ListConnectionsQueryParameters::class],
        'roles' => ['label' => 'List Roles', 'request' => ListRolesRequestParameters::class],
        'organizations' => ['label' => 'List Organizations', 'request' => ListOrganizationsRequestParameters::class],
        'resourceServers' => ['label' => 'List Resource Servers (APIs)', 'request' => ListResourceServerRequestParameters::class],
    ];

    public function __construct(
        // [AUTH0/SYMFONY] The bundle registers its service by the id `auth0`, so we autowire it by id.
        #[Autowire(service: 'auth0')]
        private Service $auth0,
    ) {
    }

    public function index(Request $request): Response
    {
        $resource = $request->query->get('resource', 'users');

        if (! array_key_exists($resource, self::RESOURCES)) {
            $resource = 'users';
        }

        $result = null;
        $error = null;
        $count = null;

        try {
            // [AUTH0/SYMFONY] getManagement() returns a v9 Management client built from the
            // bundle's existing configuration. It fetches and caches a client-credentials
            // token for you. Do NOT use getSdk()->management(), which is non-functional in v9.
            $client = $this->auth0->getManagement();

            $items = [];

            // Offset-paginated endpoints (users/clients/roles/resourceServers) only return
            // the totals envelope the v9 OffsetPager iterates over when includeTotals is set;
            // without it the API returns a bare array and the pager yields nothing. Cursor
            // endpoints (connections/organizations) use setTake() instead. The method_exists
            // guards let one path adapt across both pager types.
            $requestClass = self::RESOURCES[$resource]['request'];
            $params = new $requestClass();

            if (method_exists($params, 'setIncludeTotals')) {
                $params->setIncludeTotals(true);
            }

            if (method_exists($params, 'setPerPage')) {
                $params->setPerPage(self::PAGE_SIZE);
            }

            if (method_exists($params, 'setTake')) {
                $params->setTake(self::PAGE_SIZE);
            }

            $pager = $client->{$resource}->list($params);

            foreach ($pager as $item) {
                $items[] = $this->normalize($item);

                if (count($items) >= self::PAGE_SIZE) {
                    break;
                }
            }

            $count = count($items);
            $result = json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $throwable) {
            $error = $throwable::class . ': ' . $throwable->getMessage();
        }

        return $this->render('management.html.twig', [
            'resources' => self::RESOURCES,
            'selected' => $resource,
            'result' => $result,
            'count' => $count,
            'error' => $error,
        ]);
    }

    private function normalize(mixed $item): mixed
    {
        if (is_object($item) && method_exists($item, 'toArray')) {
            return $item->toArray();
        }

        if (is_object($item) && method_exists($item, 'jsonSerialize')) {
            return $item->jsonSerialize();
        }

        return $item;
    }
}
