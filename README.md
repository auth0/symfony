![jwt-auth-bundle](https://cdn.auth0.com/website/sdks/banners/jwt-auth-bundle-banner.png)

Symfony SDK for [Auth0](https://auth0.com) Authentication and Management APIs.

[![Package](https://img.shields.io/packagist/dt/auth0/jwt-auth-bundle)](https://packagist.org/packages/auth0/jwt-auth-bundle)
[![Build](https://img.shields.io/circleci/project/github/auth0/jwt-auth-bundle/master.svg)](https://circleci.com/gh/auth0/jwt-auth-bundle)
[![License](https://img.shields.io/packagist/l/auth0/jwt-auth-bundle)](https://doge.mit-license.org/)

:books: [Documentation](#documentation) - :rocket: [Getting Started](#getting-started) - :speech_balloon: [Feedback](#feedback)

## Documentation

- [Docs site](https://www.auth0.com/docs) — explore our docs site and learn more about Auth0.

## Getting Started

### Requirements

- [PHP](http://php.net/) 8.0+
- [Symfony](https://symfony.com/) 6.1+

> Please review our [support policy](#support-policy) to learn when language and framework versions will exit support in the future.

### Installation

Add the dependency to your application with [Composer](https://getcomposer.org/):

```
composer require auth0/jwt-auth-bundle
```

### Configure Auth0

Create a **Regular Web Application** in the [Auth0 Dashboard](https://manage.auth0.com/#/applications). Verify that the "Token Endpoint Authentication Method" is set to `POST`.

Next, configure the callback and logout URLs for your application under the "Application URIs" section of the "Settings" page:

- **Allowed Callback URLs**: The URL of your application where Auth0 will redirect to during authentication, e.g., `http://localhost:3000/callback`.
- **Allowed Logout URLs**: The URL of your application where Auth0 will redirect to after user logout, e.g., `http://localhost:3000/login`.

Note the **Domain**, **Client ID**, and **Client Secret**. These values will be used later.

### Publish SDK configuration

After installation, you will find a new file in your application, `config/packages/auth0.yaml`. (If this file isn't present, please create it manually.)

The following is an example configuration, with environment variables read from your `.env` file. It is not recommended that you include your credentials in this file directly.

```yaml
auth0:
  sdk:
    domain: "%env(string:key:host:url:AUTH0_DOMAIN)%"
    # custom_domain: "%env(string:key:host:url:AUTH0_CUSTOM_DOMAIN)%"
    client_id: "%env(trim:string:AUTH0_CLIENT_ID)%"
    client_secret: "%env(trim:string:AUTH0_CLIENT_SECRET)%"
    cookie_secret: "%kernel.secret%"
    # cookie_expires: 3600
    # cookie_path: "/"
    # cookie_secure: false
    # audiences:
    #  - "%env(trim:string:AUTH0_API_AUDIENCE)%"
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

Open the `.env` file within your application's directory, and add the following lines:

```ini
AUTH0_DOMAIN=... # Your Auth0 domain
AUTH0_CUSTOM_DOMAIN=... # Your Auth0 custom domain (if you have one)
AUTH0_CLIENT_ID=... # Your Auth0 application client ID
AUTH0_CLIENT_SECRET=... # Your Auth0 application client secret
AUTH0_API_AUDIENCE=... # Your Auth0 API identifier

# The following should be set to the same values as the routes in your application
AUTH0_ROUTE_CALLBACK=callback
AUTH0_ROUTE_LOGIN=login
AUTH0_ROUTE_SUCCESS=private
AUTH0_ROUTE_FAILURE=public
AUTH0_ROUTE_LOGOUT=public
```

### Configure your `security.yaml` file

Open your application's `config/packages/security.yaml` file, and update it based on the following example:

```yaml
security:
  providers:
    auth0_provider:
      id: Auth0\Symfony\Security\UserProvider

  firewalls:
    auth0:
      pattern: ^/auth0/private$ # An example route for stateeful/authenticated (meaning using sessions) requests
      provider: auth0_provider
      custom_authenticators:
        - auth0.authenticator
    api:
      pattern: ^/api # An example route for stateless/authorized (using access tokens) requests
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
    - { path: ^/api/private$, roles: IS_AUTHENTICATED_FULLY } # IS_AUTHENTICATED_FULLY is a special role that allows only authenticated users to access the path.
    - { path: ^/api/scoped$, roles: ROLE_USING_TOKEN } # The ROLE_USING_TOKEN role is added to users if they are authorizing using the `auth0.authorizer` authenticator (that is, using an access token.)
```

### Optional: Add Authentication helper routes

Open your application's `config/routes.yaml` file, and add the following lines:

```yaml
login: # Send the user to Auth0 for authentication.
  path: /login
  controller: Auth0\Symfony\Controllers\AuthenticationController::login

callback: # This user will be returned here from Auth0 after authentication; this is a special route that completes the authentication process. After this, the user will be redirected to the route configured as `AUTH0_ROUTE_SUCCESS` in your .env file.
  path: /callback
  controller: Auth0\Symfony\Controllers\AuthenticationController::callback

logout: # This route will clear the user's session, redirect them to Auth0 for logout and return them to the route configured as `AUTH0_ROUTE_LOGOUT` in your .env file.
  path: /logout
  controller: Auth0\Symfony\Controllers\AuthenticationController::logout
```

## Retrieving the User

```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExampleRouteController extends AbstractController
{
    public function index(): Response
    {
        return new Response(
            '<html><body><pre>' . print_r($this->getUser(), true) . '</pre> <a href="/auth0/logout">Logout</a></body></html>'
        );
    }
}

```

## Support Policy

Our support windows are determined by the [Symfony release support](https://symfony.com/doc/current/contributing/community/releases.html#maintenance) and [PHP release support](https://www.php.net/supported-versions.php) schedules, and support ends when either the Symfony framework or PHP runtime outlined below stop receiving security fixes, whichever may come first.

| SDK Version | Symfony Version¹ | PHP Version² | Support Ends³ |
| ----------- | ---------------- | ------------ | ------------- |
| 5           | 6.2              | 8.2          | Jul 2023      |
|             |                  | 8.1          | Jul 2023      |
|             |                  | 8.0          | Jul 2023      |
|             | 6.1              | 8.2          | Jan 2023      |
|             |                  | 8.1          | Jan 2023      |
|             |                  | 8.0          | Jan 2023      |

Deprecations of EOL'd language or framework versions are not considered a breaking change, as Composer handles these scenarios elegantly. Legacy applications will stop receiving updates from us, but will continue to function on those unsupported SDK versions.

## Feedback

### Contributing

We appreciate feedback and contribution to this repo! Before you get started, please see the following:

- [Auth0's general contribution guidelines](https://github.com/auth0/open-source-template/blob/master/GENERAL-CONTRIBUTING.md)
- [Auth0's code of conduct guidelines](https://github.com/auth0/open-source-template/blob/master/CODE-OF-CONDUCT.md)

### Raise an issue

To provide feedback or report a bug, [please raise an issue on our issue tracker](https://github.com/auth0/jwt-auth-bundle/issues).

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

<p align="center">Auth0 is an easy to implement, adaptable authentication and authorization platform. To learn more checkout <a href="https://auth0.com/why-auth0">Why Auth0?</a></p>

<p align="center">This project is licensed under the MIT license. See the <a href="./LICENSE"> LICENSE</a> file for more info.</p>
