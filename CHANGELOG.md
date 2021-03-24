# Change Log

## [4.0.0](https://github.com/auth0/jwt-auth-bundle/tree/4.0.0) (2021-03-23)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/3.4.0...4.0.0)

This release introduces PHP 8.0 support and upgrades the bundle to use Auth0's PHP SDK 7.x branch. It also includes expanded JWT validation options, upgraded caching support, a simplified configuration format, and other improvements.

This release includes potential breaking changes that may require minor changes to host applications to support. Please review [UPGRADING.md](UPGRADING.md) for guidance on updating your application.

**Added**

- Introduce PHP 8.0 support [\#108](https://github.com/auth0/jwt-auth-bundle/pull/108) ([olix21](https://github.com/olix21))
- Update to latest Auth0 PHP SDK version [\#108](https://github.com/auth0/jwt-auth-bundle/pull/108) ([evansims](https://github.com/evansims))
  - Configuration format updated. See README for example.
  - Cache support updated to support PSR-6 or PSR-16 caches. This cache is handed off to the Auth0 PHP SDK for use in JWK fetching.
  - Added opt-in JWT validation checks around nonce, azp, org_id, and aud claims, and support for max_age and leeway checks.
  - Enforces strict typing and expands type hinting.
  - Upgrades to PHPUnit 9, and updates unit tests to support syntax changes.
  - Adds unit tests for new helper classes.
  - Adds phpcs and phpstan checks.
- Adds support for Auth0 Organizations, currently in closed beta testing

**Changed**

- Use Symfony PSR-6 > PSR-16 cache adapter [\#110](https://github.com/auth0/jwt-auth-bundle/pull/110) ([darthf1](https://github.com/darthf1))

## [3.4.0](https://github.com/auth0/jwt-auth-bundle/tree/3.4.0) (2020-06-22)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/3.3.1...3.4.0)

**Added**

- Add support for autowiring [\#94](https://github.com/auth0/jwt-auth-bundle/pull/94) ([dunglas](https://github.com/dunglas))
- Give access to the raw JWT in the user provider [\#97](https://github.com/auth0/jwt-auth-bundle/pull/97) ([dunglas](https://github.com/dunglas))

**Changed**

- Remove unused argument, and unused property [\#95](https://github.com/auth0/jwt-auth-bundle/pull/95) ([dunglas](https://github.com/dunglas))

## [3.3.1](https://github.com/auth0/jwt-auth-bundle/tree/3.3.1) (2019-12-10)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/3.3.0...3.3.1)

**Fixed**

- Configuration authorized_issuer string or array compatibility [\#89](https://github.com/auth0/jwt-auth-bundle/pull/89) ([antzo](https://github.com/antzo))

## [3.3.0](https://github.com/auth0/jwt-auth-bundle/tree/3.3.0) (2019-12-05)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/3.2.0...3.3.0)

**Closed issues**

- new release [\#86](https://github.com/auth0/jwt-auth-bundle/issues/86)
- Remove SimplePreAuthenticatorInterface? [\#80](https://github.com/auth0/jwt-auth-bundle/issues/80)

**Added**

- Symfony 5 support [\#87](https://github.com/auth0/jwt-auth-bundle/pull/87) ([darthf1](https://github.com/darthf1))
- Multiple authorized issuer [\#85](https://github.com/auth0/jwt-auth-bundle/pull/85) ([antzo](https://github.com/antzo))

**Fixed**

- Fix deprecation Treebuilder::root [\#79](https://github.com/auth0/jwt-auth-bundle/pull/79) ([darthf1](https://github.com/darthf1))

## [3.2.0](https://github.com/auth0/jwt-auth-bundle/tree/3.2.0) (2019-09-26)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/3.1.0...3.2.0)

**Added**

- GuardAuthenticator implementation for Symfony 2.8 and later [\#75](https://github.com/auth0/jwt-auth-bundle/pull/75) ([niels-nijens](https://github.com/niels-nijens))

## [3.1.0](https://github.com/auth0/jwt-auth-bundle/tree/3.1.0) (2018-07-12)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/3.0.2...3.1.0)

**Closed issues**

- Support Symfony4 [\#55](https://github.com/auth0/jwt-auth-bundle/issues/55)
- Allow multiple audiences in config [\#54](https://github.com/auth0/jwt-auth-bundle/issues/54)

**Added**

- Add multiple audiences capability to JWT verification [\#57](https://github.com/auth0/jwt-auth-bundle/pull/57) ([joshcanhelp](https://github.com/joshcanhelp))
- Allow symfony/framework-bundle 4.x [\#56](https://github.com/auth0/jwt-auth-bundle/pull/56) ([ricbra](https://github.com/ricbra))

## [3.0.2](https://github.com/auth0/jwt-auth-bundle/tree/3.0.2) (2017-07-19)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/2.0.0...3.0.2)

**Added**

- Added support for cache [\#51](https://github.com/auth0/jwt-auth-bundle/pull/51) ([Nyholm](https://github.com/Nyholm))

## [2.0.0](https://github.com/auth0/jwt-auth-bundle/tree/2.0.0) (2016-01-29)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/1.2.8...2.0.0)

**Closed issues:**

- Symfony 3.0 Upgrade [\#24](https://github.com/auth0/jwt-auth-bundle/issues/24)
- ... but is not mandatory [\#20](https://github.com/auth0/jwt-auth-bundle/issues/20)

**Merged pull requests:**

- Symfony 3 [\#26](https://github.com/auth0/jwt-auth-bundle/pull/26) ([glena](https://github.com/glena))
- Symfony 3.0 Changes [\#25](https://github.com/auth0/jwt-auth-bundle/pull/25) ([frodosghost](https://github.com/frodosghost))

## [1.2.8](https://github.com/auth0/jwt-auth-bundle/tree/1.2.8) (2016-01-29)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/1.2.7...1.2.8)

**Merged pull requests:**

- Fix YML sintax [\#23](https://github.com/auth0/jwt-auth-bundle/pull/23) ([glena](https://github.com/glena))
- YAML files that use double quotes need to escape backslashes [\#22](https://github.com/auth0/jwt-auth-bundle/pull/22) ([frodosghost](https://github.com/frodosghost))

## [1.2.7](https://github.com/auth0/jwt-auth-bundle/tree/1.2.7) (2016-01-18)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/1.2.6...1.2.7)

**Merged pull requests:**

- updated auth0-php dependency [\#21](https://github.com/auth0/jwt-auth-bundle/pull/21) ([glena](https://github.com/glena))

## [1.2.6](https://github.com/auth0/jwt-auth-bundle/tree/1.2.6) (2015-11-17)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/1.2.5...1.2.6)

**Closed issues:**

- Setting secret_base64_encoded as false causes an exception [\#18](https://github.com/auth0/jwt-auth-bundle/issues/18)
- Installation method is incorrect [\#15](https://github.com/auth0/jwt-auth-bundle/issues/15)

**Merged pull requests:**

- \[\#18\] Remove "cannotBeEmpty" property of secret_base64_encoded [\#19](https://github.com/auth0/jwt-auth-bundle/pull/19) ([mickadoo](https://github.com/mickadoo))
- Replaces scope: 'openid profile' [\#17](https://github.com/auth0/jwt-auth-bundle/pull/17) ([aguerere](https://github.com/aguerere))

## [1.2.5](https://github.com/auth0/jwt-auth-bundle/tree/1.2.5) (2015-10-29)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/1.2.4...1.2.5)

**Closed issues:**

- Deps are wrong [\#16](https://github.com/auth0/jwt-auth-bundle/issues/16)

**Merged pull requests:**

- Fixed readme [\#14](https://github.com/auth0/jwt-auth-bundle/pull/14) ([tristanbes](https://github.com/tristanbes))
- Fixed all PSR-2 violations [\#13](https://github.com/auth0/jwt-auth-bundle/pull/13) ([tristanbes](https://github.com/tristanbes))
- Fixed typo on Symfony word [\#12](https://github.com/auth0/jwt-auth-bundle/pull/12) ([tristanbes](https://github.com/tristanbes))

## [1.2.4](https://github.com/auth0/jwt-auth-bundle/tree/1.2.4) (2015-07-17)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/1.2.3...1.2.4)

**Merged pull requests:**

- Updated JWT dependency [\#10](https://github.com/auth0/jwt-auth-bundle/pull/10) ([glena](https://github.com/glena))

## [1.2.3](https://github.com/auth0/jwt-auth-bundle/tree/1.2.3) (2015-05-15)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/1.2.2...1.2.3)

**Merged pull requests:**

- New info headers scheme [\#9](https://github.com/auth0/jwt-auth-bundle/pull/9) ([glena](https://github.com/glena))

## [1.2.2](https://github.com/auth0/jwt-auth-bundle/tree/1.2.2) (2015-05-13)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/1.2.1...1.2.2)

**Merged pull requests:**

- Added optional domain config + support for auth0-php 1.0.2 [\#8](https://github.com/auth0/jwt-auth-bundle/pull/8) ([glena](https://github.com/glena))

## [1.2.1](https://github.com/auth0/jwt-auth-bundle/tree/1.2.1) (2015-05-12)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/1.2.0...1.2.1)

**Closed issues:**

- SDK Client headers spec compliant [\#6](https://github.com/auth0/jwt-auth-bundle/issues/6)

**Merged pull requests:**

- SDK Client headers spec compliant \#6 [\#7](https://github.com/auth0/jwt-auth-bundle/pull/7) ([glena](https://github.com/glena))

## [1.2.0](https://github.com/auth0/jwt-auth-bundle/tree/1.2.0) (2015-05-08)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/1.0.0...1.2.0)

**Implemented enhancements:**

- Use auth0-php instead of custom implementation no Auth0Service [\#4](https://github.com/auth0/jwt-auth-bundle/issues/4)
- Auth0 settings should be optional [\#3](https://github.com/auth0/jwt-auth-bundle/issues/3)
- Remove auth0 dependency from the project [\#2](https://github.com/auth0/jwt-auth-bundle/issues/2)

**Closed issues:**

- Update readme [\#1](https://github.com/auth0/jwt-auth-bundle/issues/1)

**Merged pull requests:**

- Api v2 + SDK 1.0 support [\#5](https://github.com/auth0/jwt-auth-bundle/pull/5) ([glena](https://github.com/glena))

## [1.0.0](https://github.com/auth0/jwt-auth-bundle/tree/1.0.0) (2015-01-30)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/0.0.3...1.0.0)

## [0.0.3](https://github.com/auth0/jwt-auth-bundle/tree/0.0.3) (2015-01-28)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/0.0.2...0.0.3)

## [0.0.2](https://github.com/auth0/jwt-auth-bundle/tree/0.0.2) (2015-01-27)

[Full Changelog](https://github.com/auth0/jwt-auth-bundle/compare/0.0.1...0.0.2)

## [0.0.1](https://github.com/auth0/jwt-auth-bundle/tree/0.0.1) (2015-01-27)

\* _This Change Log was automatically generated by [github_changelog_generator](https://github.com/skywinder/Github-Changelog-Generator)_
