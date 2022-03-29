<?php

namespace Auth0\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @group active
 */
class BundleConfigurationTest extends TestCase
{
    /** @var \Auth0\JWTAuthBundle\DependencyInjection\JWTAuthExtension  */
    private $extension;

    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder  */
    private $container;

    /** @var string  */
    private $rootNode;

    protected function setUp(): void
    {
        $this->extension = new \Auth0\JWTAuthBundle\DependencyInjection\JWTAuthExtension();
        $this->container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        $this->rootNode = 'jwt_auth';
    }

    public function testGetConfiguration(): void
    {
        $configs = [
            'domain' => 'localhost.somewhere.auth0.com',
            'audience' => 'test audience',
            'client_id' => 'test client id',
            'client_secret' => 'test client secret',
            'authorized_issuer' => 'test.authorized.issuer',
            'algorithm' => 'RS256',
        ];

        $this->extension->load([$configs], $this->container);

        $this->assertEquals($configs['domain'], $this->container->getParameter($this->rootNode . '.domain'));
        $this->assertEquals($configs['audience'], $this->container->getParameter($this->rootNode . '.audience'));
        $this->assertEquals($configs['client_id'], $this->container->getParameter($this->rootNode . '.client_id'));
        $this->assertEquals($configs['client_secret'], $this->container->getParameter($this->rootNode . '.client_secret'));
        $this->assertEquals($configs['authorized_issuer'], $this->container->getParameter($this->rootNode . '.authorized_issuer'));
        $this->assertEquals($configs['algorithm'], $this->container->getParameter($this->rootNode . '.algorithm'));
    }

    public function testRejectInvalidAlgorithms(): void
    {
        $configs = [
            'algorithm' => 'XS256',
        ];

        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);
        $this->expectExceptionMessage('The value "XS256" is not allowed for path "jwt_auth.algorithm". Permissible values: "RS256", "HS256"');

        $this->extension->load([$configs], $this->container);
    }
}
