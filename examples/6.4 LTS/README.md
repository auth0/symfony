# Auth0 Symfony SDK Sample Application

> **Warning**
> This sample application is built for a beta version of [the Symfony SDK](https://github.com/auth0/symfony) that is currently under development and not yet intended for production use.

This sample application demonstrates how to use the Symfony SDK. It's a basic application that uses the SDK to demonstrate:

- Authenticating users with Auth0 and retrieving user profile information.
- Authorizing requests to protected routes.
- Calling the Auth0 Management API.

This sample application is built for [Symfony 6.4 LTS](https://symfony.com/). Although we strive to keep this sample up-to-date, you may need to make adjustments for newer or older versions of the framework.

If you find any issues, please help us improve our experience for other developers by submitting a pull request.

## Requirements

- PHP 8.2+
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download) (recommended)

## Setup

1. Create an Auth0 Application at https://manage.auth0.com/#/applications
2. Optional: Create an Auth0 API at https://manage.auth0.com/#/apis

Please make a note of your domain, client ID, and client secret. If you're using an API, also note your API identifier/audience.

Then, from your shell/terminal:

1. Run `composer install` to install the dependencies.
2. Run `cp .env.example .env` to create a local environment file (`.env` is gitignored, so your values are never committed).
3. Edit your `.env` file and fill in the values for the variables starting with `AUTH0_` using the details you noted above.

> **Note**
> This example requires `auth0/symfony` from Packagist. Because this is a beta release, `composer.json` uses the `^6.0@beta` constraint; once a stable 6.x is published you can use `composer require auth0/symfony`.

## Starting the application

Run the following command to start the application using the built-in web server via [the Symfony CLI](https://symfony.com/download) (recommended):

```bash
symfony server:start --no-tls
```

<details>
  <summary>Alternatively, use PHP's `-S` option ...</summary>

Note that this may provide fewer troubleshooting details in the event of errors:

```bash
php -S localhost:8000 -t public
```

</details>

## Access the application

You should now be able to access the sample from your browser at `http://localhost:8000`. The following routes are available:

### Authentication routes

These demonstrate how a traditional web application can authenticate users with Auth0.

- `/` - The home page. Shows login/logout state and links to the other routes.
- `/private` - A protected route that requires a user to be authenticated to access.
- `/login` - Begins the login flow with Auth0.
- `/callback` - The user is returned here after authenticating with Auth0. This completes the session setup and redirects to `/private`. You should not need to access this route directly.
- `/logout` - Clears the user's session and logs them out, then redirects to the route configured as `AUTH0_ROUTE_LOGOUT`.

### Authorization routes

These demonstrate how an API can authorize requests with Auth0. Note that you must uncomment the `audiences` array in `config/packages/auth0.yaml` and provide your Auth0 API identifier to use these routes.

- `/api` - A public route that does not require anything special to access.
- `/api/private` - A protected route that requires a valid token to access.
- `/api/scoped` - A protected route that requires a valid token with the `read:messages` scope to access.

### Management API route

This demonstrates calling the [Auth0 Management API](https://auth0.com/docs/api/management/v2) via the bundle's `getManagement()` accessor.

- `/management` - Iterates through read-only Management endpoints (users, clients, connections, roles, organizations, resource servers). Your Auth0 Application must be authorized for the Management API for this to return data. See [Machine-to-Machine Applications](https://auth0.com/docs/get-started/applications/machine-to-machine-apps).

## Customizing the application

You can customize the sample by altering the following files:

- The `.env.local` file's `AUTH0_*` values.
- The `config/packages/security.yaml` file for changing expected scopes or adjusting firewall settings.
- The `config/packages/auth0.yaml` file for using a custom domain or an API identifier.

> **Note**
> Comments are inserted throughout the application to identify what portions were modified from the boilerplate Symfony template. You can locate these by searching for `[AUTH0/SYMFONY]`:

```bash
grep -lr "\[AUTH0/SYMFONY\]"
```
