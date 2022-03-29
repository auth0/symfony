<?php

namespace Auth0\Tests\Unit\Security\Helpers;

use Mockery;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\JWTAuthBundle\Security\Helpers\JwtValidations;

/**
 * @group active
 */
class JwtValidationsTest extends \PHPUnit\Framework\TestCase
{
    private array $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = [
          'nonce'     => 'test_nonce',
          'azp'       => 'test_azp',
          'org_id'    => 'test_org',
          'aud'       => [
            'first_audience',
            'second_audience'
          ],
          'auth_time' => time()
        ];
    }

    public function testValidateClaimNonceMatchingNonce() {
      $this->assertTrue(JwtValidations::validateClaimNonce('test_nonce', $this->token));
    }
    public function testValidateClaimNonceUnmatchedNonce() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Nonce (nonce) claim mismatch; expected "invalid_nonce", found "test_nonce"');

      JwtValidations::validateClaimNonce('invalid_nonce', $this->token);
    }
    public function testValidateClaimNonceMissingTokenNonce() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Nonce (nonce) claim must be a string present');

      $token = $this->token;
      unset($token['nonce']);

      JwtValidations::validateClaimNonce('test_nonce', $token);
    }
    public function testValidateClaimNonceMissingToken() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Nonce (nonce) claim must be a string present');

      JwtValidations::validateClaimNonce('test_nonce', []);
    }
    public function testValidateClaimNonceMissingNonce() {
      $this->assertTrue(JwtValidations::validateClaimNonce(null, $this->token));
    }

    public function testValidateClaimAzpMatchingAzp() {
      $this->assertTrue(JwtValidations::validateClaimAzp('test_azp', $this->token));
    }
    public function testValidateClaimAzpUnmatchedAzp() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Authorized Party (azp) claim mismatch; expected "invalid_azp", found "test_azp"');

      JwtValidations::validateClaimAzp('invalid_azp', $this->token);
    }
    public function testValidateClaimAzpMissingTokenAzp() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Authorized Party (azp) claim must be a string present');

      $token = $this->token;
      unset($token['azp']);

      JwtValidations::validateClaimAzp('test_azp', $token);
    }
    public function testValidateClaimAzpMissingToken() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Authorized Party (azp) claim must be a string present');

      JwtValidations::validateClaimAzp('test_azp', []);
    }
    public function testValidateClaimAzpMissingAzp() {
      $this->assertTrue(JwtValidations::validateClaimAzp(null, $this->token));
    }

    public function testValidateClaimAudMatchingAud() {
      $this->assertTrue(JwtValidations::validateClaimAud('first_audience', $this->token));
    }
    public function testValidateClaimAudUnmatchedAud() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Audience (aud) claim mismatch; expected "missing_audience"');

      JwtValidations::validateClaimAud('missing_audience', $this->token);
    }
    public function testValidateClaimAudMissingTokenAud() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Audience (aud) claim must be a string or array of strings present');

      $token = $this->token;
      unset($token['aud']);

      JwtValidations::validateClaimAud('missing_audience', $token);
    }
    public function testValidateClaimAudMissingToken() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Audience (aud) claim must be a string or array of strings present');

      JwtValidations::validateClaimAud('missing_audience', []);
    }
    public function testValidateClaimAudMissingAud() {
      $this->assertTrue(JwtValidations::validateClaimAud(null, $this->token));
    }
    public function testValidateClaimAudMatchingTokenStringAud() {
      $token = $this->token;
      $token['aud'] = 'string_audience';
      $this->assertTrue(JwtValidations::validateClaimAud('string_audience', $token));
    }

    public function testValidateClaimOrgIdMatching() {
      $this->assertTrue(JwtValidations::validateClaimOrgId('test_org', $this->token));
    }
    public function testValidateClaimOrgIdMismatch() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Organization Id (org_id) claim value mismatch in the ID token; expected "invalid_org", found "test_org"');

      JwtValidations::validateClaimOrgId('invalid_org', $this->token);
    }
    public function testValidateClaimOrgIdMissing() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Organization Id (org_id) claim must be a string present in the ID token');

      $token = $this->token;
      unset($token['org_id']);

      JwtValidations::validateClaimOrgId('test_org', $token);
    }

    public function testValidationAgeMissingMaxAge() {
      $this->assertTrue(JwtValidations::validateAge(null, $this->token));
    }
    public function testValidationAgeMissingTokenAuthTime() {
      $this->expectException(InvalidTokenException::class);
      $this->expectExceptionMessage('Authentication Time (auth_time) claim must be a number present when Max Age (max_age) is specified');

      $token = $this->token;
      unset($token['auth_time']);

      $this->assertTrue(JwtValidations::validateAge(30, $token));
    }
    public function testValidationAgeHit() {
      $this->assertTrue(JwtValidations::validateAge(30, $this->token));
    }
    public function testValidationAgeHitWithLeeway() {
      $this->assertTrue(JwtValidations::validateAge(30, $this->token, 30));
    }
    public function testValidationAgeHitWithLeewayAndNowAssigned() {
      $this->assertTrue(JwtValidations::validateAge(30, $this->token, 30, time()));
    }
    public function testValidationAgeMissed() {
      $this->expectException(InvalidTokenException::class);

      $token = $this->token;
      $token['auth_time'] = time() - 9999;

      JwtValidations::validateAge(30, $token);
    }
    public function testValidationAgeMissedWithLeeway() {
      $this->expectException(InvalidTokenException::class);

      $token = $this->token;
      $token['auth_time'] = time() - 9999;

      JwtValidations::validateAge(30, $token, 120);
    }
    public function testValidationAgeMissedWithLeewayWithNowAssigned() {
      $this->expectException(InvalidTokenException::class);

      $token = $this->token;
      $token['auth_time'] = time() - 86400;

      $this->assertTrue(JwtValidations::validateAge(30, $token, 120, time() + 86400));
    }

    public function testValidateClaimsNone() {
      $this->assertTrue(JwtValidations::validateClaims());
    }
    public function testValidateClaimsMatchesNonce() {
      $this->assertTrue(JwtValidations::validateClaims(['nonce' => 'test_nonce'], $this->token));
    }
    public function testValidateClaimsMatchesAzp() {
      $this->assertTrue(JwtValidations::validateClaims(['azp' => 'test_azp'], $this->token));
    }
    public function testValidateClaimsMatchesAud() {
      $this->assertTrue(JwtValidations::validateClaims(['aud' => 'first_audience'], $this->token));
    }
    public function testValidateClaimsMatchesMultiple() {
      $this->assertTrue(JwtValidations::validateClaims(['nonce' => 'test_nonce', 'azp' => 'test_azp'], $this->token));
    }
    public function testValidateClaimsMissesNonce() {
      $this->expectException(InvalidTokenException::class);
      JwtValidations::validateClaims(['nonce' => 'bad_nonce'], $this->token);
    }
    public function testValidateClaimsMissesAzp() {
      $this->expectException(InvalidTokenException::class);
      JwtValidations::validateClaims(['azp' => 'bad_azp'], $this->token);
    }
    public function testValidateClaimsMissesAud() {
      $this->expectException(InvalidTokenException::class);
      JwtValidations::validateClaims(['aud' => 'missing_audience'], $this->token);
    }
    public function testValidateClaimsMissesMultiple() {
      $this->expectException(InvalidTokenException::class);
      JwtValidations::validateClaims(['nonce' => 'test_nonce', 'aud' => 'missing_audience'], $this->token);
    }
}
