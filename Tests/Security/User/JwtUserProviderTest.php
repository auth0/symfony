<?php

namespace Auth0\JWTAuthBundle\Security\User;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
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
    protected function setUp()
    {
        $this->userProvider = new JwtUserProvider();
    }

    /**
     * Tests if JwtUserProvider::supportsClass returns true for the Symfony User class.
     */
    public function testSupportsClass()
    {
        $this->assertTrue($this->userProvider->supportsClass(User::class));
    }

    /**
     * Tests if JwtUserProvider::loadUserByJWT returns the expected User instance created with information from
     * the decoded JSON Web Token.
     */
    public function testLoadUserByJWT()
    {
        $jwt = new stdClass();
        $jwt->sub = 'username';
        $jwt->token = 'validToken';

        $expectedUser = new User('username', 'validToken', ['ROLE_JWT_AUTHENTICATED']);

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
        $jwt = new stdClass();
        $jwt->sub = 'username';

        $expectedUser = new User('username', null, ['ROLE_JWT_AUTHENTICATED']);

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
        $jwt = new stdClass();
        $jwt->sub = 'username';
        $jwt->scope = 'read:messages write:messages';
        $jwt->token = 'validToken';

        $expectedUser = new User(
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
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @expectedExceptionMessage Auth0\JWTAuthBundle\Security\User\JwtUserProvider cannot load user "john.doe" by username. Use Auth0\JWTAuthBundle\Security\User\JwtUserProvider::loadUserByJWT instead.
     */
    public function testLoadUserByUsername()
    {
        $this->userProvider->loadUserByUsername('john.doe');
    }

    /**
     * Tests if JwtUserProvider::refreshUser creates a new instance from the provided User instance.
     */
    public function testRefreshUser()
    {
        $user = new User('john.doe', 'validToken', ['ROLE_JWT_AUTHENTICATED']);

        $returnedUser = $this->userProvider->refreshUser($user);

        $this->assertNotSame($user, $returnedUser);
        $this->assertEquals($user, $returnedUser);
    }

    /**
     * Tests if JwtUserProvider::refreshUser throws an UnsupportedUserException when the provided instance
     * is not of the class User.
     * @expectedException Symfony\Component\Security\Core\Exception\UnsupportedUserException
     * @expectedExceptionMessage Instances of "UnsupportedUser" are not supported.
     */
    public function testRefreshUserThrowsUnsupportedUserException()
    {
        $userMock = $this->getMockBuilder(UserInterface::class)
            ->setMockClassName('UnsupportedUser')
            ->getMock();

        $this->userProvider->refreshUser($userMock);
    }
}
