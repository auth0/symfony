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

- [PHP](http://php.net/) 7.4 or 8.0+
- [Symfony](https://symfony.com/) 4.4 or 5.4

> Support for Symfony 6 is coming in the next major update to this SDK.

> This library follows the [PHP release support schedule](https://www.php.net/supported-versions.php). We do not support PHP versions that have reached end of life and no longer receive security updates.

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

After installation, you will find a new file in your application, `config/packages/jwt_auth.yaml`.

The following is an example configuration, with environment variables read from your `.env` file.

```yaml
jwt_auth:
  #  The domain of your registered Auth0 tenant.
  domain: "%env(AUTH0_DOMAIN)%"
  # The client ID string of your registered Auth0 application.
  client_id: "%env(AUTH0_CLIENT_ID)%"
  # The audience/identifier string of your registered Auth0 API.
  audience: "%env(AUTH0_API_AUDIENCE)%"

  # Defaults to RS256. Supported options are RS256 or HS256.
  algorithm: "RS256"

  # If you're using HS256, you need to provide the client secret for your registered Auth0 application.
  client_secret: "%env(AUTH0_CLIENT_SECRET)%"

  # Recommended. A PSR-6 or PSR-16 compatible cache.
  # See: https://symfony.com/doc/current/components/cache.html
  cache: "cache.app"

  # Token validations to run during JWT decoding:
  validations:
    # Validate AUD claim against a value, such as an API identifier. Set to false to skip. Defaults to jwt_auth.audience.
    aud: "%env(AUTH0_API_AUDIENCE)%"
    # Validate the AZP claim against a value, such as a client ID. Set to false to skip. Defaults to false.
    azp: "%env(AUTH0_CLIENT_ID)%"
    # Validate ORG_ID claim against a value, such as the Auth0 Organization. Set to false to skip. Defaults to false.
    org_id: "%env(AUTH0_ORGANIZATION)%"
    # Maximum age (in seconds) since the auth_time of the token. Set to false to skip. Defaults to false.
    max_age: 3600
    # Clock tolerance (in seconds) for token expiration checks. Requires an integer value. Defaults to 60 seconds.
    leeway: 60
```

### Configure your `.env` file

Open the `.env` file within your application's directory, and add the following lines:

```ini
AUTH0_DOMAIN="Your Auth0 domain"
AUTH0_CLIENT_ID="Your Auth0 application client ID"
AUTH0_CLIENT_SECRET="Your Auth0 application client secret"
AUTH0_API_AUDIENCE="Your Auth0 API identifier"
```

## Retrieving the User

You can inject to your `UserProvider` to get the user profile, for example:

```php
<?php

namespace AppBundle\Security;

use Auth0\JWTAuthBundle\Security\Auth0Service;
use Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class A0UserProvider implements JWTUserProviderInterface
{
    protected $auth0Service;

    public function __construct(Auth0Service $auth0Service) {
        $this->auth0Service = $auth0Service;
    }

    public function loadUserByJWT($jwt) {
        // you can fetch the user profile from the auth0 api
        // or from your database
        // $data = $this->auth0Service->getUserProfileByA0UID($jwt->token,$jwt->sub);

        // in this case, we will just use what we got from
        // the token because we dont need any info from the profile
        $data = [ 'sub' => $jwt->sub ];
        $roles = array();
        $roles[] = 'ROLE_OAUTH_AUTHENTICATED';
        if (isset($jwt->scope)) {
          $scopes = explode(' ', $jwt->scope);

          if (array_search('read:messages', $scopes) !== false) {
            $roles[] = 'ROLE_OAUTH_READER';
          }
        }

        return new A0User($data, $roles);
    }

    public function loadUserByUsername($username)
    {
        throw new NotImplementedException('method not implemented');
    }

    public function getAnonymousUser() {
        return new A0AnonymousUser();
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'AppBundle\Security\A0User';
    }
}
```

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
