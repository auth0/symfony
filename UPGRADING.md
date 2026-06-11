# How to upgrade

## 5.x to 6.x

The 6.0 release moves the bundle onto [auth0-php v9](https://github.com/auth0/auth0-php), which rewrites the Management API. The authentication surface (login, logout, callback, token handling) is unchanged, so most applications only need the dependency and runtime updates below.

Check that your environment is compatible with 6.0's requirements before upgrading:

| | 5.x | 6.x |
|---|---|---|
| **PHP** | `^8.1` | `>=8.2 <8.5` |
| **auth0/auth0-php** | `^8.19` | `^9.0` |

- Please ensure you are running PHP 8.2 or newer. Support for PHP 8.1 has been dropped.
- Composer will pull in `auth0/auth0-php` v9 automatically. No package rename is required; the package remains `auth0/auth0-php`.

Update your application, if necessary:

- **Authentication flows require no changes.** The authenticator, authorizer, user provider, session store, and the bundled authentication controllers behave exactly as they did in 5.x.
- **Management API access has changed.** In v9 the old `$auth0->getSdk()->management()` entry point is non-functional and will throw a `TypeError`. Use the new `$auth0->getManagement()` accessor on the `auth0` service instead. It builds a Management client from the `domain`, `client_id`, and `client_secret` you already configure, and fetches and caches a client credentials token for you automatically.

  ```php
  // 5.x
  $management = $auth0->getSdk()->management();
  $response = $management->users()->getAll(['per_page' => 25]);
  $users = HttpResponse::decodeContent($response);

  // 6.x
  $management = $auth0->getManagement();
  $users = $management->users->list(
      new ListUsersRequestParameters(['perPage' => 25])
  );
  foreach ($users as $user) {
      echo $user->getEmail();
  }
  ```

- If your application calls the Management API directly, review the [auth0-php v9 migration guide](https://github.com/auth0/auth0-php) for the full set of changes: sub-resources are now reached by property access (`->users->list()` rather than `->users()->getAll()`), responses are typed objects instead of raw PSR-7 responses (no more `HttpResponse::decodeContent()`), errors throw exceptions, and request parameters use camelCase typed objects.

## 3.x to 4.x

Check that your environment is compatible with 4.0's requirements before upgrading:

- Please ensure you are using PHP 7.3 or newer.
- Ensure you are using Symfony 4.4 or newer.

Update your application, if necessary:

- If you wish to use JWK caching (recommended), please ensure the caching component you are passing to the SDK's configuration is either [PSR-6](https://www.php-fig.org/psr/psr-6/) or [PSR-16](https://www.php-fig.org/psr/psr-16/) compatible, such as [Symfony's cache component](https://symfony.com/doc/current/components/cache.html).
- Update your application's SDK configuration to follow the updated format outlined in the [README](README.md). Changes of note;
  - `api_identifier` is now `audience`.
  - `api_secret` is now `client_secret`.
  - `cache` requires a PSR-6/PSR-16 compatible component.
  - `api_identifier_array` and `secret_base64_encoded` are no longer used.
  - `validations` are now supported:
    - `azp` for validating a Client ID; defaults to `client_id`.
    - `aud` for validating an API identifier.
    - `org_id` for validating an Auth0 Organization ID.
    - `leeway` for the maximum age (in seconds) since the auth_time of the token.
    - `max_age` for clock tolerance (in seconds) for token expiration checks.
- Control over validations in 4.0 is new, so guidance is not required. However, it is worth noting, you must opt-in to using these validations by assigning them values, or they will be skipped.
  - The only exception is `azp`, which by default will be checked against the value of `client_id`. You can override this by simply assigning it a different value.
