<?php

namespace Auth0\JWTAuthBundle\Tests\Security;

use Auth0\JWTAuthBundle\Security\Auth0Service;
use Auth0\JWTAuthBundle\Security\JWTAuthenticator;
use Symfony\Component\HttpFoundation\Request;

class JWTAuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenCreation()
    {
        $authenticator = new JWTAuthenticator(new Auth0Service('client_id', null, null));
        $providerKey = 'providerKey';

        //generated with http://jwt.io/
        $JWT = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2RvbWFpbi5jb20vIiwic3ViIjoiYXV0aDB8MDAwMDAwMDAwMDAwMDAwMDAwMDAwMCIsImF1ZCI6ImNsaWVudF9pZCIsImV4cCI6MTQyMjQ0MDI3MSwiaWF0IjoxNDIyNDA0MjcxfQ.xSuCAetwfHpCWhE_5NqTrwHq0eQ7CVffQwgSqTHwwrY';

        $request = $this->getMock('Request');
        $request->headers = $this->getMock('ParameterBag');
        $request->request
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('Authorization')
            ->will($this->returnValue($JWT));

        $token = $authenticator->createToken($request, $providerKey);

        $this->assertEquals('anon.',$token->GetUser());
        $this->assertEquals($providerKey,$token->getProviderKey());
        $this->assertEquals($providerKey,$token->getProviderKey());
    }
}
