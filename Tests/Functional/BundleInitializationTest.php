<?php

namespace Auth0\JWTAuthBundle\Tests\Security;


use Auth0\JWTAuthBundle\JWTAuthBundle;
use Auth0\JWTAuthBundle\Security\Auth0Service;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;


class BundleInitializationTest extends BaseBundleTestCase
{
    protected function setUp(): void
    {
        // Make all services public
        $this->addCompilerPass(new PublicServicePass());
    }

    protected function getBundleClass()
    {
        return JWTAuthBundle::class;
    }

    public function testInitBundle()
    {
        // Boot the kernel.
        $this->bootKernel();

        // Get the container
        $container = $this->getContainer();

        // Test if you services exists
        $this->assertTrue($container->has('jwt_auth.auth0_service'));
        $service = $container->get('jwt_auth.auth0_service');
        $this->assertInstanceOf(Auth0Service::class, $service);

        // Test if autowiring is working properly
        $this->assertTrue($container->has(Auth0Service::class));
        $service = $container->get(Auth0Service::class);
        $this->assertInstanceOf(Auth0Service::class, $service);
    }

    public function testBundleWithCache()
    {
        // Create a new Kernel
        $kernel = $this->createKernel();

        // Add some configuration
        $kernel->addConfigFile(__DIR__.'/config/cache.yml');

        // Boot the kernel as normal ...
        $this->bootKernel();

        $container = $this->getContainer();
        $service = $container->get('jwt_auth.auth0_service');
        $this->assertInstanceOf(Auth0Service::class, $service);
    }
}
