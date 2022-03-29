# jwt-auth-bundle

JWT Authentication bundle for Symfony

[![Build Status](https://img.shields.io/circleci/project/github/auth0/jwt-auth-bundle/master.svg)](https://circleci.com/gh/auth0/jwt-auth-bundle) [![Total Downloads](https://img.shields.io/packagist/dt/auth0/jwt-auth-bundle)](https://packagist.org/packages/auth0/jwt-auth-bundle) [![Latest Stable Version](https://img.shields.io/packagist/v/auth0/jwt-auth-bundle?label=stable)](https://packagist.org/packages/auth0/jwt-auth-bundle) [![PHP Support](https://img.shields.io/packagist/php-v/auth0/jwt-auth-bundle)](https://packagist.org/packages/auth0/jwt-auth-bundle) [![Code Coverage](https://codecov.io/gh/auth0/jwt-auth-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/auth0/jwt-auth-bundle) [![License](https://img.shields.io/packagist/l/auth0/jwt-auth-bundle)](https://packagist.org/packages/auth0/jwt-auth-bundle) [![FOSSA](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fauth0%2Fjwt-auth-bundle.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Fauth0%2Fjwt-auth-bundle?ref=badge_shield)

## Requirements

- [PHP](http://php.net/) 7.3+
- [Symfony](https://symfony.com/) 4.4+
- [Auth0 PHP](https://github.com/auth0/auth0-PHP) 7.6+

## Installation

Using [Composer](https://getcomposer.org/doc/00-intro.md):

```bash
composer require auth0/jwt-auth-bundle:"~4.0"
```

## Configuration

After installing the bundle in your project you should find a new file located at `config/packages/jwt_auth.yaml`. These values should read from variables set in your `.env` file. Available configuration options are:

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

Sample for `config/security.yaml`
```
security:
    # The option below is required if you want to use the jwt_auth.security.guard.jwt_authenticator service (Sf >= 5.1)
    enable_authenticator_manager: true
    firewalls:
        main:
            pattern:   ^/
            provider: web_service_user_provider
            # For Symfony >= 5.1
            custom_authenticators:
                - jwt_auth.security.guard.jwt_authenticator  
            # For Symfony < 5.1
            guard:
                authenticators:
                    - jwt_auth.security.guard.jwt_guard_authenticator
            
```

## Auth0 integration

The [Auth0 PHP SDK](https://github.com/auth0/auth0-PHP) is included in this bundle to handle the processing of JWTs. You can inject to your `UserProvider` to get the user profile, [example code](https://github.com/auth0-community/auth0-symfony-api-samples/blob/master/01-Authorization-RS256/src/AppBundle/Security/A0UserProvider.php).

## Contributing

We appreciate your feedback and contributions to the project! Before you get started, please review the following:

- [Auth0's general contribution guidelines](https://github.com/auth0/open-source-template/blob/master/GENERAL-CONTRIBUTING.md)
- [Auth0's code of conduct guidelines](https://github.com/auth0/open-source-template/blob/master/CODE-OF-CONDUCT.md)
- [The Auth0 PHP SDK contribution guide](CONTRIBUTING.md)

## Support + Feedback

- The [Auth0 Community](https://community.auth0.com/) is a valuable resource for asking questions and finding answers, staffed by the Auth0 team and a community of enthusiastic developers
- For code-level support (such as feature requests and bug reports) we encourage you to [open issues](https://github.com/auth0/auth0-PHP/issues) here on our repo
- For customers on [paid plans](https://auth0.com/pricing/), our [support center](https://support.auth0.com/) is available for opening tickets with our knowledgeable support specialists

Further details about our support solutions are [available on our website.](https://auth0.com/docs/support)

## Vulnerability Reporting

Please do not report security vulnerabilities on the public GitHub issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

## What is Auth0?

Auth0 helps you to:

- Add authentication with [multiple authentication sources](https://docs.auth0.com/identityproviders), either social like Google, Facebook, Microsoft, LinkedIn, GitHub, Twitter, Box, Salesforce (amongst others), or enterprise identity systems like Windows Azure AD, Google Apps, Active Directory, ADFS or any SAML Identity Provider.
- Add authentication through more traditional **[username/password databases](https://docs.auth0.com/mysql-connection-tutorial)**.
- Add support for [passwordless](https://auth0.com/passwordless) and [multi-factor authentication](https://auth0.com/docs/mfa).
- Add support for [linking different user accounts](https://docs.auth0.com/link-accounts) with the same user.
- Analytics of how, when and where users are logging in.
- Pull data from other sources and add it to the user profile, through [JavaScript rules](https://docs.auth0.com/rules).

[Why Auth0?](https://auth0.com/why-auth0)

## License

This project is open source software licensed under [the MIT license](https://opensource.org/licenses/MIT). See the [LICENSE](LICENSE) file for more info.

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fauth0%2Fjwt-auth-bundle.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fauth0%2Fjwt-auth-bundle?ref=badge_large)
