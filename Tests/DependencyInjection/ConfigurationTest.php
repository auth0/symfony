<?php

namespace Auth0\JWTAuthBundle\Tests\DependencyInjection;

use Auth0\JWTAuthBundle\DependencyInjection\JWTAuthExtension;
use Auth0\JWTAuthBundle\JWTAuthBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigurationTest extends TestCase
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

    public function testGetConfigWhenMultipleApiIdentifier()
    {
        $configs = [
            'api_identifier_array' => [
                'test identifier1',
                'test identifier2'
            ],
            'api_client_id' => 'test client id'
        ];

        $this->extension->load([$configs], $this->container);

        $this->assertTrue($this->container->hasParameter($this->rootNode . '.api_identifier'));
        $this->assertEquals(
            [
                'test identifier1',
                'test identifier2',
                'test client id'
            ],
            $this->container->getParameter($this->rootNode . '.api_identifier')
        );
    }

    public function testGetConfigWhenSingleApiIdentifier()
    {
        $configs = [
            'api_identifier' => 'test identifier'
        ];

        $this->extension->load([$configs], $this->container);

        $this->assertTrue($this->container->hasParameter($this->rootNode . '.api_identifier'));
        $this->assertEquals('test identifier', $this->container->getParameter($this->rootNode . '.api_identifier'));
    }

    public function testGetConfigWhenMultipleAuthorizedIssuer()
    {
        $configs = [
            'authorized_issuer' => [
                'test authorized issuer1',
                'test authorized issuer2'
            ]
        ];

        $this->extension->load([$configs], $this->container);

        $this->assertTrue($this->container->hasParameter($this->rootNode . '.authorized_issuer'));
        $this->assertEquals(
            [
                'test authorized issuer1',
                'test authorized issuer2'
            ],
            $this->container->getParameter($this->rootNode . '.authorized_issuer')
        );
    }

    public function testGetConfigWhenSingleAuthorizedIssuer()
    {
        $configs = [
            'authorized_issuer' => 'test authorized issuer'
        ];

        $this->extension->load([$configs], $this->container);

        $this->assertTrue($this->container->hasParameter($this->rootNode . '.authorized_issuer'));
        $this->assertEquals('test authorized issuer', $this->container->getParameter($this->rootNode . '.authorized_issuer'));
    }
}
