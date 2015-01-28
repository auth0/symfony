#Auth0\JWTAuth bundle usage demo

For the jwy-auth package check: https://github.com/auth0/jwt-auth-bundle

###1. Install dependencies

We recommend using [Composer](http://getcomposer.org/doc/01-basic-usage.md) to install the library.

Modify your `composer.json` to add the following dependencies and run `composer update`.

~~~js
{
    "require": {
        "firebase/php-jwt": "dev-master",
        "adoy/oauth2": "dev-master",
        "auth0/jwt-auth": "0.0.2"
    }
}
~~~

###2. Configure your Auth0 app data

Modify the file /app/config/config.yml

~~~yml
auth0_symfony_jwt:
    domain:        yourdomain.auth0.com
    client_id:     YOURCLIENTID
    client_secret: YOURCLIENTSECRET
    redirect_url:  http://localhost:8000/auth0/callback
~~~

###3. Setup your User and UserProvider

Create your User and UserProvider.
The UserProvider must implements the JWTUserProviderInterface (see /source/AppBundle/Security/A0UserProvider).

The configure your services on /app/config/services.yml

~~~yml
services:
    a0_user_provider:
        class: AppBundle\Security\A0UserProvider
        arguments: ["@auth0_symfony_jwt.auth0_service"]
~~~

###4. Setup the SecurityProvider

Modify the file /app/config/security.yml

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
                authenticator: auth0_symfony_jwt.jwt_authenticator
~~~



