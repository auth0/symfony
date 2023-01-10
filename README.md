![auth0/symfony](https://cdn.auth0.com/website/sdks/banners/jwt-auth-bundle-banner.png)

Symfony SDK for [Auth0](https://auth0.com) Authentication and Management APIs.

:books: [Documentation](#documentation) - :rocket: [Getting Started](#getting-started) - :speech_balloon: [Feedback](#feedback)

## Documentation

- [Docs site](https://www.auth0.com/docs) — explore our docs site and learn more about Auth0.

## Getting Started

### Requirements

- [PHP](http://php.net/) 8.1+
- [Symfony](https://symfony.com/) 6.1+

> Please review our [support policy](#support-policy) to learn when language and framework versions will exit support in the future.

### Installation

Add the dependency to your application with [Composer](https://getcomposer.org/):

```
composer require auth0/symfony
```

### Configure Auth0

Create a **Regular Web Application** in the [Auth0 Dashboard](https://manage.auth0.com/#/applications). Verify that the "Token Endpoint Authentication Method" is set to `POST`.

Next, configure the callback and logout URLs for your application under the "Application URIs" section of the "Settings" page:

- **Allowed Callback URLs**: URL of your application where Auth0 will redirect to during authentication, e.g., `http://localhost:8000/callback`.
- **Allowed Logout URLs**: URL of your application where Auth0 will redirect to after logout, e.g., `http://localhost:8000/login`.

Note the **Domain**, **Client ID**, and **Client Secret**. These values will be used later.

### Configure the SDK

After installation, you should find a new file in your application, `config/packages/auth0.yaml`. If this file isn't present, please create it manually.

The following is an example configuration that will use environment variables to assign values. You should avoid storing sensitive credentials directly in this file, as it will often be committed to version control.

```yaml
auth0:
  sdk:
    domain: "%env(trim:string:AUTH0_DOMAIN)%"
    client_id: "%env(trim:string:AUTH0_CLIENT_ID)%"
    client_secret: "%env(trim:string:AUTH0_CLIENT_SECRET)%"
    cookie_secret: "%kernel.secret%"

    # custom_domain: "%env(trim:string:AUTH0_CUSTOM_DOMAIN)%"

    # audiences:
    #  - "%env(trim:string:AUTH0_API_AUDIENCE)%"

    # token_cache: cache.auth0_token_cache
    # management_token_cache: cache.auth0_management_token_cache

    scopes:
      - openid
      - profile
      - email
      - offline_access

  authenticator:
    routes:
      callback: "%env(string:AUTH0_ROUTE_CALLBACK)%"
      success: "%env(string:AUTH0_ROUTE_SUCCESS)%"
      failure: "%env(string:AUTH0_ROUTE_FAILURE)%"
      login: "%env(string:AUTH0_ROUTE_LOGIN)%"
      logout: "%env(string:AUTH0_ROUTE_LOGOUT)%"
```

### Configure your `.env` file

Create or open a `.env.local` file within your application directory, and add the following lines:

```ini
#
# ↓ Refer to your Auth0 application details (https://manage.auth0.com/#/applications) for these values.
#

# Your Auth0 application domain
AUTH0_DOMAIN=...

# Your Auth0 application client ID
AUTH0_CLIENT_ID=...

# Your Auth0 application client secret
AUTH0_CLIENT_SECRET=...

# Optional. Your Auth0 custom domain, if you have one. (https://manage.auth0.com/#/custom_domains)
AUTH0_CUSTOM_DOMAIN=...

# Optional. Your Auth0 API identifier/audience, if used. (https://manage.auth0.com/#/apis)
AUTH0_API_AUDIENCE=...

#
# ↓ These routes will be used by the SDK to direct traffic during authentication.
#

# The route that SDK will redirect to after authentication:
AUTH0_ROUTE_CALLBACK=callback

# The route that will trigger the authentication process:
AUTH0_ROUTE_LOGIN=login

# The route that the SDK will redirect to after a successful authentication:
AUTH0_ROUTE_SUCCESS=private

# The route that the SDK will redirect to after a failed authentication:
AUTH0_ROUTE_FAILURE=public

# The route that the SDK will redirect to after a successful logout:
AUTH0_ROUTE_LOGOUT=public
```

Please ensure this `.env.local` file is included in your `.gitignore`. It should never be committed to version control.

### Configure your `security.yaml` file

Open your application's `config/packages/security.yaml` file, and update it based on the following example:

```yaml
security:
  providers:
    auth0_provider:
      id: Auth0\Symfony\Security\UserProvider

  firewalls:
    auth0:
      pattern: ^/private$ # A pattern example for stateful (session-based authentication) route requests
      provider: auth0_provider
      custom_authenticators:
        - auth0.authenticator
    api:
      pattern: ^/api # A pattern example for stateless (token-based authorization) route requests
      stateless: true
      provider: auth0_provider
      custom_authenticators:
        - auth0.authorizer
    dev:
      pattern: ^/(_(profiler|wdt)|css|images|js)/
      security: false
    main:
      lazy: true

  access_control:
    - { path: ^/api$, roles: PUBLIC_ACCESS } # PUBLIC_ACCESS is a special role that allows everyone to access the path.
    - { path: ^/api/scoped$, roles: ROLE_USING_TOKEN } # The ROLE_USING_TOKEN role is added by the Auth0 SDK to any request that includes a valid access token.
    - { path: ^/api/scoped$, roles: ROLE_READ_MESSAGES } # This route will expect the given access token to have the `read:messages` scope in order to access it.
```

### Optional: Add Authentication helper routes

The SDK includes a number of pre-built HTTP controllers that can be used to handle authentication. These controllers are not required, but can be helpful in getting started. In many cases, these may provide all the functionality you need to integrate Auth0 into your application, providing a plug-and-play solution.

To use these, open your application's `config/routes.yaml` file, and add the following lines:

```yaml
login: # Send the user to Auth0 for authentication.
  path: /login
  controller: Auth0\Symfony\Controllers\AuthenticationController::login

callback: # This user will be returned here from Auth0 after authentication; this is a special route that completes the authentication process. After this, the user will be redirected to the route configured as `AUTH0_ROUTE_SUCCESS` in your .env file.
  path: /callback
  controller: Auth0\Symfony\Controllers\AuthenticationController::callback

logout: # This route will clear the user's session and return them to the route configured as `AUTH0_ROUTE_LOGOUT` in your .env file.
  path: /logout
  controller: Auth0\Symfony\Controllers\AuthenticationController::logout
```

### Recommended: Configure caching

The SDK provides two caching properties in it's configuration: `token_cache` and `management_token_cache`. These are compatible with any PSR-6 cache implementation, of which Symfony offers several out of the box.

These are used to store JSON Web Key Sets (JWKS) results for validating access token signatures and generated management API tokens, respectively. We recommended configuring this feature to improve your application's performance by reducing the number of network requests the SDK needs to make. It will also greatly help in avoiding hitting rate-limiting conditions, if you're making frequent Management API requests.

The following is an example `config/packages/cache.yaml` file that would configure the SDK to use a Redis backend for caching:

```yaml
framework:
  cache:
    prefix_seed: auth0_symfony_sample

    app: cache.adapter.redis
    default_redis_provider: redis://localhost

    pools:
      auth0_token_cache: { adapter: cache.adapter.redis }
      auth0_management_token_cache: { adapter: cache.adapter.redis }
```

Please review [the Symfony cache documentation](https://symfony.com/doc/current/components/cache.html#cache-component-psr6-caching) for adapter-specific configuration options. Please note that the SDK does not currently support Symfony's "Cache Contract" adapter type.

### Example: Retrieving the User

The following example shows how to retrieve the authenticated user within a controller. For this example, we'll create a mock `ExampleController` class that is accessible from a route at `/private`.

Add a route to your application's `config/routes.yaml` file:

```yaml
private:
  path: /private
  controller: App\Controller\ExampleController::private
```

Now update or create a `src/Controller/ExampleController.php` class to include the following code:

```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExampleController extends AbstractController
{
    public function private(): Response
    {
        return new Response(
            '<html><body><pre>' . print_r($this->getUser(), true) . '</pre> <a href="/auth0/logout">Logout</a></body></html>'
        );
    }
}
```

If you visit the `/private` route in your browser, you should see the authenticated user's details. If you are not already authenticated, you will be redirected to the `/login` route to login, and then back to `/private` afterward.

## Support Policy

Our support windows are determined by the [Symfony release support](https://symfony.com/doc/current/contributing/community/releases.html#maintenance) and [PHP release support](https://www.php.net/supported-versions.php) schedules, and support ends when either the Symfony framework or PHP runtime outlined below stop receiving security fixes, whichever may come first.

| SDK Version | Symfony Version | PHP Version | Support Ends |
| ----------- | --------------- | ----------- | ------------ |
| 5           | 6.2             | 8.2         | Jul 31 2023  |
|             |                 | 8.1         | Jul 31 2023  |
|             | 6.1             | 8.2         | Jan 31 2023  |
|             |                 | 8.1         | Jan 31 2023  |

Deprecations of EOL'd language or framework versions are not considered a breaking change, as Composer handles these scenarios elegantly. Legacy applications will stop receiving updates from us, but will continue to function on those unsupported SDK versions.

## Feedback

### Contributing

We appreciate feedback and contribution to this repo! Before you get started, please see the following:

- [Auth0's general contribution guidelines](https://github.com/auth0/open-source-template/blob/master/GENERAL-CONTRIBUTING.md)
- [Auth0's code of conduct guidelines](https://github.com/auth0/open-source-template/blob/master/CODE-OF-CONDUCT.md)

### Raise an issue

To provide feedback or report a bug, [please raise an issue on our issue tracker](https://github.com/auth0/symfony/issues).

### Vulnerability Reporting

Please do not report security vulnerabilities on the public Github issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

---

<p align="center">
  <picture>
    <source media="(prefers-color-scheme: light)" srcset="https://cdn.auth0.com/website/sdks/logos/auth0_light_mode.png" width="150">
    <source media="(prefers-color-scheme: dark)" srcset="https://cdn.auth0.com/website/sdks/logos/auth0_dark_mode.png" width="150">
    <img alt="Auth0 Logo" src="https://cdn.auth0.com/website/sdks/logos/auth0_light_mode.png" width="150">
  </picture>
</p>

<p align="center">Auth0 is an easy to implement, adaptable authentication and authorization platform.<br />To learn more checkout <a href="https://auth0.com/why-auth0">Why Auth0?</a></p>

<p align="center">This project is licensed under the MIT license. See the <a href="./LICENSE"> LICENSE</a> file for more info.</p>
