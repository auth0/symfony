<?php

namespace Auth0\Tests\Unit;

use Auth0\JWTAuthBundle\DependencyInjection\JWTAuthExtension;
use Auth0\JWTAuthBundle\JWTAuthBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BundleConfigurationTest extends TestCase
{
    /** @var JWTAuthBundle  */
    private $extension;

    /** @var ContainerBuilder  */
    private $container;

    /** @var string  */
    private $rootNode;

    protected function setUp(): void
    {
        $this->extension = new JWTAuthExtension();
        $this->container = new ContainerBuilder();
        $this->rootNode = 'jwt_auth';
    }

    public function testGetConfiguration()
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

    public function testRejectInvalidAlgorithms()
    {
        $configs = [
            'algorithm' => 'XS256',
        ];

        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);
        $this->expectExceptionMessage('The value "XS256" is not allowed for path "jwt_auth.algorithm". Permissible values: "RS256", "HS256"');

        $this->extension->load([$configs], $this->container);
    }
}
