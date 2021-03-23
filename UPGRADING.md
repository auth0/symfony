# How to upgrade

## 3.x to 4.x

Check that your environment is compatible with 4.0's requirements before upgrading:

- Please ensure you are using PHP 7.3 or newer.
- Ensure you are using Symfony 4.4 or newer.

Update your application, if necessary:

- If you wish to use JWK caching (recommended), please ensure the caching component you are passing to jwt-auth-bundle's configuration is either [PSR-6](https://www.php-fig.org/psr/psr-6/) or [PSR-16](https://www.php-fig.org/psr/psr-16/) compatible, such as [Symfony's cache component](https://symfony.com/doc/current/components/cache.html).
- Update your application's jwt-auth-bundle configuration to follow the updated format outlined in the [README](README.md). Changes of note;
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
