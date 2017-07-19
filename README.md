# jwt-auth-bundle

JWT Authentication bundle for Symfony

## Requirements

- [PHP](http://php.net/) 5.5+
- [Symfony](https://symfony.com/) 2.8+
- [Auth0 PHP](https://github.com/auth0/auth0-PHP) 5.0+

> For Symfony < 2.8 please see [v1](https://github.com/auth0/jwt-auth-bundle/tree/1.x.x-dev)

## Installation

To install the dependency, run the following:

```bash
composer require auth0/jwt-auth-bundle:"~3.0"
```

> For more information about Composer usage, check [their official documentation](https://getcomposer.org/doc/00-intro.md).

## Resources

Check out the [Symfony API QuickStart Guide](https://auth0.com/docs/quickstart/backend/symfony) to find out more about integrating the bunlde into an existing project or download a pre-configured project.

## Demo

[Symfony API Samples](https://github.com/auth0-community/auth0-symfony-api-samples)

## Auth0 integration

The Auth0 PHP SDK is used to decode the JWT and if you are woking with Auth0 you can inject to your `UserProvider` to get the user profile, [example code](https://github.com/auth0-community/auth0-symfony-api-samples/blob/master/01-Authorization-RS256/src/AppBundle/Security/A0UserProvider.php).

## Issue Reporting

If you have found a bug or if you have a feature request, please report them at this repository issues section. Please do not report security vulnerabilities on the public GitHub issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

## Author

[Auth0](auth0.com)

## License

This project is licensed under the MIT license. See the [LICENSE](LICENSE) file for more info.
