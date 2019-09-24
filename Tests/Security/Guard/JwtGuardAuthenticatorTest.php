<?php

namespace Auth0\JWTAuthBundle\Tests\Security\Guard;

use Auth0\JWTAuthBundle\Security\Auth0Service;
use Auth0\JWTAuthBundle\Security\Core\JWTUserProviderInterface;
use Auth0\JWTAuthBundle\Security\Guard\JwtGuardAuthenticator;
use Auth0\SDK\Exception\InvalidTokenException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Tests the @see JwtGuardAuthenticator.
 */
class JwtGuardAuthenticatorTest extends TestCase
{
    /**
     * @var JwtGuardAuthenticator
     */
    private $guardAuthenticator;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $authZeroServiceMock;

    /**
     * Creates a JwtGuardAuthenticator instance for testing.
     */
    protected function setUp()
    {
        $this->authZeroServiceMock = $this->getMockBuilder(Auth0Service::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->guardAuthenticator = new JwtGuardAuthenticator($this->authZeroServiceMock);
    }

    /**
     * Tests if JwtGuardAuthenticator::supports returns false when the Request does not contain an Authorization header.
     */
    public function testSupportsReturnsFalseWhenRequestDoesNotContainAuthorizationHeader()
    {
        $request = Request::create('/');

        $this->assertFalse($this->guardAuthenticator->supports($request));
    }

    /**
     * Tests if JwtGuardAuthenticator::supports returns true when the Request contains an Authorization header.
     */
    public function testSupportsReturnsTrueWhenRequestContainsAuthorizationHeader()
    {
        $request = Request::create('/');
        $request->headers->set('Authorization', 'Bearer token');

        $this->assertTrue($this->guardAuthenticator->supports($request));
    }

    /**
     * Tests if JwtGuardAuthenticator::getCredentials returns null when the Request does not contain
     * an Authorization header.
     */
    public function testGetCredentialsReturnsNullWhenRequestDoesNotContainAuthorizationHeader()
    {
        $request = Request::create('/');

        $this->assertNull($this->guardAuthenticator->getCredentials($request));
    }

    /**
     * Tests if JwtGuardAuthenticator::getCredentials returns an array with the 'jwt' key (that should
     * contain the JWT token) when the Request contains an Authorization header.
     */
    public function testGetCredentialsReturnsArrayWithJwtWhenRequestContainsAuthorizationHeader()
    {
        $request = Request::create('/');
        $request->headers->set('Authorization', 'Bearer token');

        $this->assertSame(
            array('jwt' => 'token'),
            $this->guardAuthenticator->getCredentials($request)
        );
    }

    /**
     * Tests if JwtGuardAuthenticator::getUser return an unknown User instance when decoding the JSON Web Token fails,
     * because verification of the token must be done during the JwtGuardAuthenticator::checkCredentials step.
     */
    public function testGetUserReturnsUnknownUserWhenJwtDecodingFails()
    {
        $this->authZeroServiceMock->expects($this->once())
            ->method('decodeJWT')
            ->with('invalidToken')
            ->willThrowException(new InvalidTokenException('Malformed token.'));

        $userProviderMock = $this->getMockBuilder(JWTUserProviderInterface::class)
            ->getMock();
        $userProviderMock->expects($this->never())
            ->method('loadUserByJWT');
        $userProviderMock->expects($this->never())
            ->method('loadUserByUsername');

        $user = $this->guardAuthenticator->getUser(
            array('jwt' => 'invalidToken'),
            $userProviderMock
        );

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('unknown', $user->getUsername());
        $this->assertNull($user->getPassword());
        $this->assertSame(array(), $user->getRoles());
    }

    /**
     * Tests if JwtGuardAuthenticator::getUser calls the JWTUserProviderInterface::loadUserByJWT when the provided
     * user provider implements the interface.
     */
    public function testGetUserReturnsUserThroughLoadUserByJWT()
    {
        $jwt = new stdClass();
        $jwt->sub = 'authenticated-user';
        $jwt->token = 'validToken';

        $this->authZeroServiceMock->expects($this->once())
            ->method('decodeJWT')
            ->with('validToken')
            ->willReturn($jwt);

        $user = new User($jwt->sub, $jwt->token, array('ROLE_JWT_AUTHENTICATED'));

        $userProviderMock = $this->getMockBuilder(JWTUserProviderInterface::class)
            ->getMock();
        $userProviderMock->expects($this->once())
            ->method('loadUserByJWT')
            ->with($jwt)
            ->willReturn($user);

        $returnedUser = $this->guardAuthenticator->getUser(
            array('jwt' => 'validToken'),
            $userProviderMock
        );

        $this->assertSame($user, $returnedUser);
    }

    /**
     * Tests if JwtGuardAuthenticator::getUser calls the UserProviderInterface::loadUserByUsername when the provided
     * user provider does not implement the JWTUserProviderInterface.
     */
    public function testGetUserReturnsUserThroughLoadUserByUsername()
    {
        $jwt = new stdClass();
        $jwt->sub = 'authenticated-user';
        $jwt->token = 'validToken';

        $this->authZeroServiceMock->expects($this->once())
            ->method('decodeJWT')
            ->with('validToken')
            ->willReturn($jwt);

        $user = new User($jwt->sub, null, array('ROLE_JWT_AUTHENTICATED'));

        $userProviderMock = $this->getMockBuilder(UserProviderInterface::class)
            ->getMock();
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->with($jwt->sub)
            ->willReturn($user);

        $returnedUser = $this->guardAuthenticator->getUser(array('jwt' => 'validToken'), $userProviderMock);

        $this->assertSame($user, $returnedUser);
    }

    /**
     * Tests if JwtGuardAuthenticator::checkCredentials throws an AuthenticationException containing the information
     * from the exception thrown by the Auth0Service.
     */
    public function testCheckCredentialsThrowsAuthenticationExceptionWhenJwtDecodingFails()
    {
        $this->authZeroServiceMock->expects($this->once())
            ->method('decodeJWT')
            ->with('invalidToken')
            ->willThrowException(new InvalidTokenException('Malformed token.'));

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Malformed token.');

        $this->guardAuthenticator->checkCredentials(
            array('jwt' => 'invalidToken'),
            new User('unknown', null)
        );
    }

    /**
     * Tests if JwtGuardAuthenticator::checkCredentials returns true when decoding the JSON Web Token is successful.
     */
    public function testCheckCredentialsReturnsTrueWhenJwtDecodingSuccessful()
    {
        $this->authZeroServiceMock->expects($this->once())
            ->method('decodeJWT')
            ->with('validToken')
            ->willReturn(new stdClass());

        $this->assertTrue(
            $this->guardAuthenticator->checkCredentials(
                array('jwt' => 'validToken'),
                new User('unknown', null)
            )
        );
    }

    /**
     * Tests if JwtGuardAuthenticator::onAuthenticationSuccess does not return a Response, meaning the application will
     * continue with the original request.
     */
    public function testOnAuthenticationSuccess()
    {
        $request = Request::create('/');

        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->getMock();

        $this->assertNull(
            $this->guardAuthenticator->onAuthenticationSuccess($request, $tokenMock, 'providerKey')
        );
    }

    /**
     * Tests if JwtGuardAuthenticator::onAuthenticationFailure returns JSON response with a '403 Forbidden' status code.
     */
    public function testOnAuthenticationFailure()
    {
        $request = Request::create('/');
        $exception = new AuthenticationException('Malformed token.', 0, new InvalidTokenException('Malformed token.'));

        $response = $this->guardAuthenticator->onAuthenticationFailure($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            '{"message": "Authentication failed: Malformed token."}',
            $response->getContent()
        );
    }

    /**
     * Tests if JwtGuardAuthenticator::start returns a JSON response with a '401 Unauthorized' status code.
     */
    public function testStart()
    {
        $request = Request::create('/');

        $response = $this->guardAuthenticator->start($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            '{"message": "Authentication required."}',
            $response->getContent()
        );
    }

    /**
     * Tests if JwtGuardAuthenticator::supportsRememberMe returns false. The Authorization header must be provided
     * with every request.
     */
    public function testSupportsRememberMe()
    {
        $this->assertFalse($this->guardAuthenticator->supportsRememberMe());
    }
}
