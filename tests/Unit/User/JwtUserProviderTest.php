<?php

namespace Auth0\Tests\Unit\User;

use Auth0\JWTAuthBundle\Security\Auth0Service;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Tests the @see JwtUserProvider.
 */
class JwtUserProviderTest extends TestCase
{
    /**
     * @var JwtUserProvider
     */
    private $userProvider;

    /**
     * Creates a JwtUserProvider instance for testing.
     */
    protected function setUp(): void
    {
        $auth0Service = $this->getMockBuilder(Auth0Service::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->userProvider = new JwtUserProvider($auth0Service);
    }

    /**
     * Tests if JwtUserProvider::supportsClass returns true for the Symfony User class.
     */
    public function testSupportsClass()
    {
        $this->assertTrue($this->userProvider->supportsClass(InMemoryUser::class));
    }

    /**
     * Tests if JwtUserProvider::loadUserByJWT returns the expected User instance created with information from
     * the decoded JSON Web Token.
     */
    public function testLoadUserByJWT()
    {
        $jwt = new \stdClass();
        $jwt->sub = 'username';
        $jwt->token = 'validToken';

        $expectedUser = new InMemoryUser('username', 'validToken', ['ROLE_JWT_AUTHENTICATED']);

        $this->assertEquals(
            $expectedUser,
            $this->userProvider->loadUserByJWT($jwt)
        );
    }

    /**
     * Tests if JwtUserProvider::loadUserByJWT returns the expected User instance created with information from
     * the decoded JSON Web Token without token property.
     */
    public function testLoadUserByJWTWithoutTokenProperty()
    {
        $jwt = new \stdClass();
        $jwt->sub = 'username';

        $expectedUser = new InMemoryUser('username', null, ['ROLE_JWT_AUTHENTICATED']);

        $this->assertEquals(
            $expectedUser,
            $this->userProvider->loadUserByJWT($jwt)
        );
    }

    /**
     * Tests if JwtUserProvider::loadUserByJWT returns the expected User instance created with information from
     * the decoded JSON Web Token.
     *
     * The scopes in the scope property will be transformed into Symfony compatible roles.
     */
    public function testLoadUserByJWTWithScopeProperty()
    {
        $jwt = new \stdClass();
        $jwt->sub = 'username';
        $jwt->scope = 'read:messages write:messages';
        $jwt->token = 'validToken';

        $expectedUser = new InMemoryUser(
            'username',
            'validToken',
            ['ROLE_JWT_AUTHENTICATED', 'ROLE_JWT_SCOPE_READ_MESSAGES', 'ROLE_JWT_SCOPE_WRITE_MESSAGES']
        );

        $this->assertEquals(
            $expectedUser,
            $this->userProvider->loadUserByJWT($jwt)
        );
    }

    /**
     * Tests if JwtUserProvider::getAnonymousUser returns nothing. Although, it must be implemented according
     * to the JWTUserProviderInterface.
     */
    public function testGetAnonymousUser()
    {
        $this->assertNull($this->userProvider->getAnonymousUser());
    }

    /**
     * Tests if JwtUserProvider::loadUserByUsername throws a UsernameNotFoundException with a message that
     * the method should not be used.
     */
    public function testLoadUserByUsername()
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Auth0\JWTAuthBundle\Security\User\JwtUserProvider cannot load user "john.doe" by username. Use Auth0\JWTAuthBundle\Security\User\JwtUserProvider::loadUserByJWT instead.');

        $this->userProvider->loadUserByUsername('john.doe');
    }

    /**
     * Tests if JwtUserProvider::refreshUser creates a new instance from the provided User instance.
     */
    public function testRefreshUser()
    {
        $user = new InMemoryUser('john.doe', 'validToken', ['ROLE_JWT_AUTHENTICATED']);

        $returnedUser = $this->userProvider->refreshUser($user);

        $this->assertNotSame($user, $returnedUser);
        $this->assertEquals($user, $returnedUser);
    }

    /**
     * Tests if JwtUserProvider::refreshUser throws an UnsupportedUserException when the provided instance
     * is not of the class User.
     */
    public function testRefreshUserThrowsUnsupportedUserException()
    {
        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage('Instances of "UnsupportedUser" are not supported.');

        $userMock = $this->getMockBuilder(UserInterface::class)
            ->setMockClassName('UnsupportedUser')
            ->getMock();

        $this->userProvider->refreshUser($userMock);
    }
}
