# Change Log

## [5.5.0](https://github.com/auth0/symfony/tree/5.4.0) (2025-09-02)
[Full Changelog](https://github.com/auth0/symfony/compare/5.4.1...5.5.0)

**Fixed**

-   Security fix: Resolve CVE-2025-47275

## [5.4.1](https://github.com/auth0/symfony/tree/5.4.1) (2025-09-02)
[Full Changelog](https://github.com/auth0/symfony/compare/5.4.0...5.4.1)

**Fixed**
- fix: Remove unused ext-json requirement from composer.json [\#209](https://github.com/auth0/symfony/pull/209) ([kishore7snehil](https://github.com/kishore7snehil))

## [5.4.0](https://github.com/auth0/symfony/tree/5.4.0) (2025-05-16)
[Full Changelog](https://github.com/auth0/symfony/compare/5.3.1...5.4.0)

**Fixed**

-   Security fix: Resolve CVE-2025-47275

## [5.3.1](https://github.com/auth0/symfony/tree/5.3.1) (2025-05-13)
[Full Changelog](https://github.com/auth0/symfony/compare/5.3.0...5.3.1)

**Fixed**
- fix : Unit Tests [\#200](https://github.com/auth0/symfony/pull/200) ([kishore7snehil](https://github.com/kishore7snehil))
- Removed Deprecated Rules From PHP CS Fixer [\#195](https://github.com/auth0/symfony/pull/195) ([kishore7snehil](https://github.com/kishore7snehil))

## [5.3.0](https://github.com/auth0/symfony/tree/5.3.0) (2024-06-24)

[Full Changelog](https://github.com/auth0/symfony/compare/5.2.3...5.3.0)

This release includes experimental community-contributed support for Symfony 7. If you encounter any issues, please [open an issue on GitHub](https://github.com/auth0/symfony/issues).

**Added**

- Support for Symfony 7. [\#188](https://github.com/auth0/symfony/pull/188) ([mkilmanas](https://github.com/mkilmanas))
- Support string concatenation of scopes. [\#183](https://github.com/auth0/symfony/pull/183) ([mkilmanas](https://github.com/mkilmanas))

**Changed**

- Dashes in JWT permissions/scopes are now normalized. [\#184](https://github.com/auth0/symfony/pull/184) ([mkilmanas](https://github.com/mkilmanas))

**Fixed**

- Fixed an issue in controller constructors using a `$container` argument. [\#190)](https://github.com/auth0/symfony/pull/190) ([mkilmanas](https://github.com/mkilmanas))

## [5.2.3](https://github.com/auth0/symfony/tree/5.2.3) (2024-01-08)

[Full Changelog](https://github.com/auth0/symfony/compare/5.2.2...5.2.3)

**Fixed**

- Syntax typo in AuthenticationController::\_\_construct() [\#180](https://github.com/auth0/symfony/pull/180) ([mkilmanas](https://github.com/mkilmanas))
- Controller container property assignment [\#179](https://github.com/auth0/symfony/pull/179) ([mkilmanas](https://github.com/mkilmanas))

## [5.2.2](https://github.com/auth0/symfony/tree/5.2.2) (2023-12-19)

[Full Changelog](https://github.com/auth0/symfony/compare/5.2.1...5.2.2)

**Fixed**

- Disallow installation with Symfony 7.0 until fully compatible

## [5.2.1](https://github.com/auth0/symfony/tree/5.2.1) (2023-12-16)

[Full Changelog](https://github.com/auth0/symfony/compare/5.2.0...5.2.1)

**Fixed**

- Restore method signatures [\#174](https://github.com/auth0/symfony/pull/174) ([evansims](https://github.com/evansims))

## [5.2.0](https://github.com/auth0/symfony/tree/5.2.0) (2023-12-12)

**Added**

- Implement support for Back-Channel Logout [\#167](https://github.com/auth0/wordpress/pull/167) ([evansims](https://github.com/evansims)) ¹

**Changed**

- Bumped `auth0-php` dependency version range to `^8.10`.
- Raised the minimum supported PHP version to `8.1`.
- Added support for Symfony `^6.4`.
  - Symfony `^7.0` support will be added in a forthcoming release.

> [!NOTE]
> ¹ To use this feature, an Auth0 tenant must have support for it enabled.

## [5.1.0](https://github.com/auth0/symfony/tree/5.1.0) (2023-07-24)

**Added**

- Organization Name support added for Authentication API and token handling ¹

**Changed**

- Bumped `auth0-php` dependency version range to `^8.7`.
- Updated telemetry to indicate new `symfony` package name (previously `jwt-auth-bundle`.)

> **Note**
> ¹ To use this feature, an Auth0 tenant must have support for it enabled. This feature is not yet available to all tenants.

## [5.0.0](https://github.com/auth0/symfony/tree/5.0.0) (2023-01-10)

[Full Changelog](https://github.com/auth0/symfony/compare/4.0.0...5.0.0)

We are excited to announce the release of V5.0 of Auth0's Symfony SDK! This version is a complete rewrite of our Symfony bundle, and includes full support for PHP 8.1 and Symfony 6.1. It also includes numerous new features and greatly expanded functionality, including:

- Plug-and-play route controllers to instantly add Auth0 authentication to your Symfony application
- Expanded route authorization support
- Support for Symfony's new Guard APIs
- Improved performance and stability
- Full integration with v8 of the [Auth0-PHP SDK](https://github.com/auth0/auth0-PHP) and all of its features, including Management APIs, passwordless, and more

This release represents a significant upgrade to the API of our bundle, and we'd encourage you to refer to our updated [README.md](README.md) for usage of the new release, and guidance on upgrading your Symfony application. We hope you enjoy this new version and all the features it has to offer. Thank you for using Auth0!

> **Note:** As of this release, we have renamed the package to `auth0/symfony` (previously `auth0/jwt-auth-bundle`) so as to better clarify the elevated functionality of the SDK into a full Auth0 integration with this release. We have marked the `auth0/jwt-auth-bundle` package as deprecated with Packagist to inform customers of this change. Simply update your `composer.json` to reference the new package name to continue receiving updates.

## [5.0.0 BETA-1](https://github.com/auth0/symfony/tree/5.0.0-BETA1) (2022-12-15)

[Full Changelog](https://github.com/auth0/symfony/compare/5.0.0-BETA0...5.0.0-BETA1)

> **Warning** This SDK is in beta and is subject to breaking changes. It is not recommended for production use, but your feedback and help in testing is appreciated!

This release introduces PHP 8.0 support, Symfony 6.1+ support, and upgrades the bundle to use Auth0's Auth0-PHP SDK 8.x branch. It also introduces a new configuration format, full authorization support, and other improvements. Please review the updated [README.md](README.md) for guidance on updating your application.

**Added**

- Integration with Symfony session management APIs [\#141](https://github.com/auth0/symfony/pull/141) ([evansims](https://github.com/evansims))
- Integration with Symfony caching component APIs [\#140](https://github.com/auth0/symfony/pull/140) ([evansims](https://github.com/evansims))

**Fixed**

- Minor bug fixes and performance improvements from BETA-0

## [5.0.0 BETA-0](https://github.com/auth0/symfony/tree/5.0.0-BETA0) (2022-12-04)

[Full Changelog](https://github.com/auth0/symfony/compare/4.0.0...5.0.0-BETA0)

> **Warning** This SDK is in beta and is subject to breaking changes. It is not recommended for production use, but your feedback and help in testing is appreciated!

This release introduces PHP 8.0 support, Symfony 6.1+ support, and upgrades the bundle to use Auth0's Auth0-PHP SDK 8.x branch. It also introduces a new configuration format, full authorization support, and other improvements. Please review the updated [README.md](README.md) for guidance on updating your application.

## [4.0.0](https://github.com/auth0/symfony/tree/4.0.0) (2021-03-23)

[Full Changelog](https://github.com/auth0/symfony/compare/3.4.0...4.0.0)

This release introduces PHP 8.0 support and upgrades the bundle to use Auth0's PHP SDK 7.x branch. It also includes expanded JWT validation options, upgraded caching support, a simplified configuration format, and other improvements.

This release includes potential breaking changes that may require minor changes to host applications to support. Please review [UPGRADING.md](UPGRADING.md) for guidance on updating your application.

**Added**

- Introduce PHP 8.0 support [\#108](https://github.com/auth0/symfony/pull/108) ([olix21](https://github.com/olix21))
- Update to latest Auth0 PHP SDK version [\#108](https://github.com/auth0/symfony/pull/108) ([evansims](https://github.com/evansims))
  - Configuration format updated. See README for example.
  - Cache support updated to support PSR-6 or PSR-16 caches. This cache is handed off to the Auth0 PHP SDK for use in JWK fetching.
  - Added opt-in JWT validation checks around nonce, azp, org_id, and aud claims, and support for max_age and leeway checks.
  - Enforces strict typing and expands type hinting.
  - Upgrades to PHPUnit 9, and updates unit tests to support syntax changes.
  - Adds unit tests for new helper classes.
  - Adds phpcs and phpstan checks.
- Adds support for Auth0 Organizations, currently in closed beta testing

**Changed**

- Use Symfony PSR-6 > PSR-16 cache adapter [\#110](https://github.com/auth0/symfony/pull/110) ([darthf1](https://github.com/darthf1))

## [3.4.0](https://github.com/auth0/symfony/tree/3.4.0) (2020-06-22)

[Full Changelog](https://github.com/auth0/symfony/compare/3.3.1...3.4.0)

**Added**

- Add support for autowiring [\#94](https://github.com/auth0/symfony/pull/94) ([dunglas](https://github.com/dunglas))
- Give access to the raw JWT in the user provider [\#97](https://github.com/auth0/symfony/pull/97) ([dunglas](https://github.com/dunglas))

**Changed**

- Remove unused argument, and unused property [\#95](https://github.com/auth0/symfony/pull/95) ([dunglas](https://github.com/dunglas))

## [3.3.1](https://github.com/auth0/symfony/tree/3.3.1) (2019-12-10)

[Full Changelog](https://github.com/auth0/symfony/compare/3.3.0...3.3.1)

**Fixed**

- Configuration authorized_issuer string or array compatibility [\#89](https://github.com/auth0/symfony/pull/89) ([antzo](https://github.com/antzo))

## [3.3.0](https://github.com/auth0/symfony/tree/3.3.0) (2019-12-05)

[Full Changelog](https://github.com/auth0/symfony/compare/3.2.0...3.3.0)

**Closed issues**

- new release [\#86](https://github.com/auth0/symfony/issues/86)
- Remove SimplePreAuthenticatorInterface? [\#80](https://github.com/auth0/symfony/issues/80)

**Added**

- Symfony 5 support [\#87](https://github.com/auth0/symfony/pull/87) ([darthf1](https://github.com/darthf1))
- Multiple authorized issuer [\#85](https://github.com/auth0/symfony/pull/85) ([antzo](https://github.com/antzo))

**Fixed**

- Fix deprecation Treebuilder::root [\#79](https://github.com/auth0/symfony/pull/79) ([darthf1](https://github.com/darthf1))

## [3.2.0](https://github.com/auth0/symfony/tree/3.2.0) (2019-09-26)

[Full Changelog](https://github.com/auth0/symfony/compare/3.1.0...3.2.0)

**Added**

- GuardAuthenticator implementation for Symfony 2.8 and later [\#75](https://github.com/auth0/symfony/pull/75) ([niels-nijens](https://github.com/niels-nijens))

## [3.1.0](https://github.com/auth0/symfony/tree/3.1.0) (2018-07-12)

[Full Changelog](https://github.com/auth0/symfony/compare/3.0.2...3.1.0)

**Closed issues**

- Support Symfony4 [\#55](https://github.com/auth0/symfony/issues/55)
- Allow multiple audiences in config [\#54](https://github.com/auth0/symfony/issues/54)

**Added**

- Add multiple audiences capability to JWT verification [\#57](https://github.com/auth0/symfony/pull/57) ([joshcanhelp](https://github.com/joshcanhelp))
- Allow symfony/framework-bundle 4.x [\#56](https://github.com/auth0/symfony/pull/56) ([ricbra](https://github.com/ricbra))

## [3.0.2](https://github.com/auth0/symfony/tree/3.0.2) (2017-07-19)

[Full Changelog](https://github.com/auth0/symfony/compare/2.0.0...3.0.2)

**Added**

- Added support for cache [\#51](https://github.com/auth0/symfony/pull/51) ([Nyholm](https://github.com/Nyholm))

## [2.0.0](https://github.com/auth0/symfony/tree/2.0.0) (2016-01-29)

[Full Changelog](https://github.com/auth0/symfony/compare/1.2.8...2.0.0)

**Closed issues:**

- Symfony 3.0 Upgrade [\#24](https://github.com/auth0/symfony/issues/24)
- ... but is not mandatory [\#20](https://github.com/auth0/symfony/issues/20)

**Merged pull requests:**

- Symfony 3 [\#26](https://github.com/auth0/symfony/pull/26) ([glena](https://github.com/glena))
- Symfony 3.0 Changes [\#25](https://github.com/auth0/symfony/pull/25) ([frodosghost](https://github.com/frodosghost))

## [1.2.8](https://github.com/auth0/symfony/tree/1.2.8) (2016-01-29)

[Full Changelog](https://github.com/auth0/symfony/compare/1.2.7...1.2.8)

**Merged pull requests:**

- Fix YML sintax [\#23](https://github.com/auth0/symfony/pull/23) ([glena](https://github.com/glena))
- YAML files that use double quotes need to escape backslashes [\#22](https://github.com/auth0/symfony/pull/22) ([frodosghost](https://github.com/frodosghost))

## [1.2.7](https://github.com/auth0/symfony/tree/1.2.7) (2016-01-18)

[Full Changelog](https://github.com/auth0/symfony/compare/1.2.6...1.2.7)

**Merged pull requests:**

- updated auth0-php dependency [\#21](https://github.com/auth0/symfony/pull/21) ([glena](https://github.com/glena))

## [1.2.6](https://github.com/auth0/symfony/tree/1.2.6) (2015-11-17)

[Full Changelog](https://github.com/auth0/symfony/compare/1.2.5...1.2.6)

**Closed issues:**

- Setting secret_base64_encoded as false causes an exception [\#18](https://github.com/auth0/symfony/issues/18)
- Installation method is incorrect [\#15](https://github.com/auth0/symfony/issues/15)

**Merged pull requests:**

- \[\#18\] Remove "cannotBeEmpty" property of secret_base64_encoded [\#19](https://github.com/auth0/symfony/pull/19) ([mickadoo](https://github.com/mickadoo))
- Replaces scope: 'openid profile' [\#17](https://github.com/auth0/symfony/pull/17) ([aguerere](https://github.com/aguerere))

## [1.2.5](https://github.com/auth0/symfony/tree/1.2.5) (2015-10-29)

[Full Changelog](https://github.com/auth0/symfony/compare/1.2.4...1.2.5)

**Closed issues:**

- Deps are wrong [\#16](https://github.com/auth0/symfony/issues/16)

**Merged pull requests:**

- Fixed readme [\#14](https://github.com/auth0/symfony/pull/14) ([tristanbes](https://github.com/tristanbes))
- Fixed all PSR-2 violations [\#13](https://github.com/auth0/symfony/pull/13) ([tristanbes](https://github.com/tristanbes))
- Fixed typo on Symfony word [\#12](https://github.com/auth0/symfony/pull/12) ([tristanbes](https://github.com/tristanbes))

## [1.2.4](https://github.com/auth0/symfony/tree/1.2.4) (2015-07-17)

[Full Changelog](https://github.com/auth0/symfony/compare/1.2.3...1.2.4)

**Merged pull requests:**

- Updated JWT dependency [\#10](https://github.com/auth0/symfony/pull/10) ([glena](https://github.com/glena))

## [1.2.3](https://github.com/auth0/symfony/tree/1.2.3) (2015-05-15)

[Full Changelog](https://github.com/auth0/symfony/compare/1.2.2...1.2.3)

**Merged pull requests:**

- New info headers scheme [\#9](https://github.com/auth0/symfony/pull/9) ([glena](https://github.com/glena))

## [1.2.2](https://github.com/auth0/symfony/tree/1.2.2) (2015-05-13)

[Full Changelog](https://github.com/auth0/symfony/compare/1.2.1...1.2.2)

**Merged pull requests:**

- Added optional domain config + support for auth0-php 1.0.2 [\#8](https://github.com/auth0/symfony/pull/8) ([glena](https://github.com/glena))

## [1.2.1](https://github.com/auth0/symfony/tree/1.2.1) (2015-05-12)

[Full Changelog](https://github.com/auth0/symfony/compare/1.2.0...1.2.1)

**Closed issues:**

- SDK Client headers spec compliant [\#6](https://github.com/auth0/symfony/issues/6)

**Merged pull requests:**

- SDK Client headers spec compliant \#6 [\#7](https://github.com/auth0/symfony/pull/7) ([glena](https://github.com/glena))

## [1.2.0](https://github.com/auth0/symfony/tree/1.2.0) (2015-05-08)

[Full Changelog](https://github.com/auth0/symfony/compare/1.0.0...1.2.0)

**Implemented enhancements:**

- Use auth0-php instead of custom implementation no Auth0Service [\#4](https://github.com/auth0/symfony/issues/4)
- Auth0 settings should be optional [\#3](https://github.com/auth0/symfony/issues/3)
- Remove auth0 dependency from the project [\#2](https://github.com/auth0/symfony/issues/2)

**Closed issues:**

- Update readme [\#1](https://github.com/auth0/symfony/issues/1)

**Merged pull requests:**

- Api v2 + SDK 1.0 support [\#5](https://github.com/auth0/symfony/pull/5) ([glena](https://github.com/glena))

## [1.0.0](https://github.com/auth0/symfony/tree/1.0.0) (2015-01-30)

[Full Changelog](https://github.com/auth0/symfony/compare/0.0.3...1.0.0)

## [0.0.3](https://github.com/auth0/symfony/tree/0.0.3) (2015-01-28)

[Full Changelog](https://github.com/auth0/symfony/compare/0.0.2...0.0.3)

## [0.0.2](https://github.com/auth0/symfony/tree/0.0.2) (2015-01-27)

[Full Changelog](https://github.com/auth0/symfony/compare/0.0.1...0.0.2)

## [0.0.1](https://github.com/auth0/symfony/tree/0.0.1) (2015-01-27)

\* _This Change Log was automatically generated by [github_changelog_generator](https://github.com/skywinder/Github-Changelog-Generator)_
