# jwt-auth-bundle

JWT Authentication bundle for Symphony


##Demo

Check the usage demo: https://github.com/auth0/jwt-auth-bundle/tree/master/example

##News

###Version 1.2 release

- PSR-4 compliance
- Support for API v2 & auth0-php 1.0

#### BC breaks

- Config
    - package name space changed to `jwt_auth`
    - added optional `secret_base64_encoded` field
- Removed dependency with Auth0. Now you can use non base64 tokens.
- The profile data is not longer a `stdClass`, is an asociative array.
- There are some BC issues related to the auth0-php changes. We recomment to read the [auth0-php README](https://github.com/auth0/Auth0-PHP).

#### Auth0 integration

This package has built in Auth0 integration (as you can check on the example) but is not mandatory. You can use this package to authenticate other JWT.

The auth-php SDK is used to decode the JWT and if you are woking with Auth0 you can inject to your `UserProvider` to get the user profile (as you can check on the [example](https://github.com/auth0/jwt-auth-bundle/blob/master/example/src/AppBundle/Security/A0UserProvider.php)).

##Usage

###1. Install dependencies

We recommend using [Composer](http://getcomposer.org/doc/01-basic-usage.md) to install the library.

Modify your `composer.json` to add the following dependencies and run `composer update`.

~~~js
{
    "require": {
        "firebase/php-jwt": "dev-master",
        "adoy/oauth2": "dev-master",
        "auth0/jwt-auth-bundle": "~1.1"
    }
}
~~~

###2. Add the bundle to your AppKernell.php file

~~~php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(

            ...

            new \Auth0\JWTAuthBundle\JWTAuthBundle(),

            ...

        );

        ...

        return $bundles;
    }

~~~

###3. Configure your Auth0 app data

Modify the file /app/config/config.yml

~~~yml
jwt-auth-bundle:
    client_id:     YOURCLIENTID
    client_secret: YOURCLIENTSECRET
    domain: (optional) YOURAUTH0DOMAIN (ie: tenant.auth0.com)
    secret_base64_encoded: (optional) TRUE if the secret is base64 encoded (true by default as the Auth0 secret)
~~~

###4. Setup your User and UserProvider

Create your User and UserProvider.

The UserProvider must implements the JWTUserProviderInterface (see /source/AppBundle/Security/A0UserProvider). This class should implement 2 methods:
- loadUserByJWT: This method receives the decoded JWT (but overloaded with the encoded token on the token attribute) and should return a User.
- getAnonymousUser: This method should return an anonymous user that represents an unauthenticated one (usually represented by the role *IS_AUTHENTICATED_ANONYMOUSLY*).
*Both methods can throw an AuthenticationException exception in case that the user is not found, in the case of the loadUserByJWT method, or you don't want to handle unauthenticated users on your app, in the case of the getAnonymousUser method.*

The configure your services on /app/config/services.yml

~~~yml
services:
    a0_user_provider:
        class: AppBundle\Security\A0UserProvider
        arguments: ["@jwt_auth.auth0_service"]
~~~

###5. Setup the SecurityProvider

Modify the file /app/config/security.yml:

- define your user provider
- define your secured area that want to authenticate using JWT
- define the access_control section with the roles needed for each route

~~~yml
security:
    providers:
        a0:
            id:
                a0_user_provider

    firewalls:
        secured_area:
            pattern: ^/api
            stateless: true
            simple_preauth:
                authenticator: jwt_auth.jwt_authenticator

    access_control:
        - { path: ^/api/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/api, roles: ROLE_OAUTH_USER }
~~~


## Issue Reporting

If you have found a bug or if you have a feature request, please report them at this repository issues section. Please do not report security vulnerabilities on the public GitHub issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.
