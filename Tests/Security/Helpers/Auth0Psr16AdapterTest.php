<?php

namespace Auth0\JWTAuthBundle\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Mockery;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Auth0\JWTAuthBundle\Security\Helpers\Auth0Psr16Adapter;

class Auth0Psr16AdapterTest extends TestCase
{
    private $adapter;
    private $mockPool;
    private $mockItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockPool = Mockery::mock(CacheItemPoolInterface::class, 'CacheItemPoolInterface');
        $this->mockItem = Mockery::mock(CacheItemInterface::class, 'CacheItemInterface');
        $this->adapter = new Auth0Psr16Adapter($this->mockPool);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(Auth0Psr16Adapter::class, $this->adapter);
    }

    public function testFetch()
    {
        $this->mockItem->shouldReceive('isHit')->times(1)->andReturn(true);
        $this->mockItem->shouldReceive('get')->times(1)->andReturn('some_value');
        $this->mockPool->shouldReceive('getItem')->withArgs(['some_item'])->andReturn($this->mockItem);
        $this->assertEquals('some_value', $this->adapter->get('some_item'));
    }

    public function testFetchMiss()
    {
        $this->mockItem->shouldReceive('isHit')->times(1)->andReturn(false);
        $this->mockPool->shouldReceive('getItem')->withArgs(['no_item'])->andReturn($this->mockItem);
        $this->assertFalse($this->adapter->get('no_item', false));
    }

    public function testContains()
    {
        $this->mockPool->shouldReceive('hasItem')->withArgs(['no_item'])->andReturn(false);
        $this->mockPool->shouldReceive('hasItem')->withArgs(['some_item'])->andReturn(true);
        $this->assertFalse($this->adapter->has('no_item'));
        $this->assertTrue($this->adapter->has('some_item'));
    }

    public function testSave()
    {
        $this->mockItem->shouldReceive('set')->twice()->with('dummy_data');
        $this->mockItem->shouldReceive('expiresAfter')->once()->with(null);
        $this->mockItem->shouldReceive('expiresAfter')->once()->with(2);
        $this->mockPool->shouldReceive('getItem')->twice()->with('some_item')->andReturn($this->mockItem);
        $this->mockPool->shouldReceive('save')->twice()->with($this->mockItem)->andReturn(true);
        $this->assertTrue($this->adapter->set('some_item', 'dummy_data'));
        $this->assertTrue($this->adapter->set('some_item', 'dummy_data', 2));
    }

    public function testDelete()
    {
        $this->mockPool->shouldReceive('deleteItem')->once()->with('some_item')->andReturn(true);
        $this->assertTrue($this->adapter->delete('some_item'));
    }
}
