<?php

namespace Auth0\Tests\Unit\Security\Guard;

use Auth0\JWTAuthBundle\Security\Auth0Service;
use Auth0\JWTAuthBundle\Security\Guard\JwtAuthenticator;
use Auth0\SDK\Exception\CoreException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtAuthenticatorTest extends TestCase
{
    /** @var JwtAuthenticator */
    private $authenticator;

    /** @var Auth0Service|MockObject */
    private $auth0Service;

    protected function setUp(): void
    {
        $this->auth0Service = $this->getMockBuilder(Auth0Service::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->authenticator = new JwtAuthenticator($this->auth0Service);
    }

    public function testJwtAuthenticatorDontSupportWithoutAuthorizationHeader()
    {
        $request = Request::create('/');
        $this->assertFalse($this->authenticator->supports($request));
    }

    public function testAuthenticateMethodFailIfNoAuthorizationHeaderInRequest()
    {
        $request = Request::create('/');
        $this->expectExceptionMessage('JWT is missing in the request Authorization header');

        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateFailsIfAuthorizationHeaderIsNotABearer()
    {
        $request = Request::create('/');
        $request->headers->set('Authorization', 'someInvalidStuff');
        $this->expectExceptionMessage('JWT is not a bearer token');

        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateFailsIfJwtDecodeFails()
    {
        $request = Request::create('/');
        $request->headers->set('Authorization', 'Bearer invalidToken');

        $this->auth0Service->expects($this->once())
            ->method('decodeJWT')
            ->with('invalidToken')
            ->willThrowException(new CoreException('invalid token'))
        ;

        $this->expectException(AuthenticationException::class);

        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateFailsIfDecodedJwtIsEmpty()
    {
        $request = Request::create('/');
        $request->headers->set('Authorization', 'Bearer token');

        $this->auth0Service->expects($this->once())
            ->method('decodeJWT')
            ->with('token')
            ->willReturn(null)
        ;

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Your JWT seems invalid');

        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateCanSuccess()
    {
        $request = Request::create('/');
        $request->headers->set('Authorization', 'Bearer amazingToken');

        $jwt = new \stdClass();
        $jwt->sub = 'authenticated-user';
        $jwt->token = 'amazingToken';
        $this->auth0Service->expects($this->once())
            ->method('decodeJWT')
            ->with('amazingToken')
            ->willReturn($jwt)
        ;

        $passport = $this->authenticator->authenticate($request);
        $this->assertInstanceOf(SelfValidatingPassport::class, $passport);


        $fakeUser = new class implements UserInterface {
            public function getRoles(): array { return []; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return ''; }
            // Symfony <6.0
            public function getPassword() {}
            public function getSalt() {}
            public function getUsername() {}
        };

        $userProviderHasBeenCalled = false;
        $userProvider = function($jwtString) use($fakeUser, &$userProviderHasBeenCalled) {
            $this->assertSame('amazingToken', $jwtString);
            $userProviderHasBeenCalled = true;
            return $fakeUser;
        };
        $userBadge = $passport->getBadge(UserBadge::class);
        // Registering the user provider on the user badge is done by a symfony listener.
        $userBadge->setUserLoader($userProvider);

        $this->assertSame($fakeUser, $passport->getUser());
        $this->assertTrue($userProviderHasBeenCalled);
    }
}
