# Auth0 Symfony SDK Sample Application

> **Warning**
> This sample application is built for a beta version of <a href="https://github.com/auth0/jwt-auth-bundle">the Symfony SDK</a> that is currently under development and not yet intended for production use.

This sample application demonstrates how to use the Symfony SDK. It's a basic application that uses the SDK to demonstrate:

- Authenticating users with Auth0 and retrieving user profile information.
- Authorizing requests to protected routes.

This sample application is built for the [Symfony 6](https://symfony.com/) framework. Although we strive to keep this sample up-to-date, you may need to make adjustments for newer or older versions of the framework.

If you find any issues, please help us improve our experience for other developers by submitting a pull request.

## Requirements

- PHP 8.1+
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download) (recommended)

## Setup

From your shell/terminal, run the following commands to get started:

1. Create an Auth0 Application at https://manage.auth0.com/#/applications
2. Optional: Create an Auth0 API at https://manage.auth0.com/#/apis

Please make a note of your domain, client ID, and client secret. If you're using an API, also note your API identifier/audience. You will need these in the next steps.

Complete application setup by using the quick setup script:

1. Run `chmod +x ./setup.sh` to make the quick setup script executable
2. Run `./setup.sh` to install the dependencies and follow the instructions to create a local environment file

<details>
  <summary>Alternatively, setup the application manually ...</summary>

1. Run `composer install` to install the dependencies
2. Run `cp .env .env.local` to create a local environment file
3. Edit your `.env.local` file and fill in the values for variables starting with `AUTH0_` using the details you noted above
</details>

## Starting the application

Run the following command to start the application using the built-in web server using [the Symfony CLI](https://symfony.com/download) (recommended):

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

You should now be able to access the sample from your browser at `http://localhost:8000`. You'll find the following routes are available for use:

### Authentication routes

These demonstrate how a traditional web application can authenticate users with Auth0.

- `/` - This route serves as a health check to ensure the app is running successfully. It does not leverage the SDK.
- `/private` - This is a protected route that requires a user to be authenticated to access.
- `/login` - This route begins the login flow with Auth0. It sets up the user session and redirects to Auth0 for authentication.
- `/callback` - The user is returned to this route after authenticating with Auth0. This finishes setting up the user session and redirects to the `/private` route. You should not need to access this route directly.
- `/logout` - This route clears the user's session and logs them out. It will briefly redirect them to Auth0 to clear their session there as well, then redirect them back to the home page (or whatever route is configured with `AUTH0_ROUTE_LOGOUT` in your `.env.local` file.)

### Authorization routes

These demonstrate how an API can authorize requests with Auth0. Note that you must uncomment the `audiences` array in the `config/packages/auth0.yaml` file to use these routes, and provide the identifier for your Auth0 API.

- `/api` - A public route that does not require anything special to access.
- `/api/private` - A protected route that requires a valid token to access.
- `/api/scoped` - A protected route that requires a valid token with the `read:messages` scope to access.

## Customizing the application

You can customize the sample to suit your needs by altering the following files:

- The `.env.local` file's AUTH0\_\* values
- The `config/packages/security.yaml` file for changing expected scopes or adjusting firewall settings
- The `config/packages/auth0.yaml` file for using a custom domain or an API identifier

> **Note**
> Comments have been inserted throughout the application to identify what portions were modified from the boilerplate Symfony template application. You can locate these by searching for `[AUTH0/SYMFONY]` within the _.yaml and _.php files. For example:

```bash
grep -lr "\[AUTH0\/SYMFONY\]"
```
