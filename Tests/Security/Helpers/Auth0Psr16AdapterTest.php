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
    private $mockItems;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockPool = Mockery::mock(CacheItemPoolInterface::class, 'CacheItemPoolInterface');
        $this->mockItems = [
            Mockery::mock(CacheItemInterface::class, 'CacheItemInterface'),
            Mockery::mock(CacheItemInterface::class, 'CacheItemInterface'),
        ];

        $this->adapter = new Auth0Psr16Adapter($this->mockPool);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(Auth0Psr16Adapter::class, $this->adapter);
    }

    public function testFetch()
    {
        $this->mockItems[0]->shouldReceive('isHit')->times(1)->andReturn(true);
        $this->mockItems[0]->shouldReceive('get')->times(1)->andReturn('some_value');
        $this->mockPool->shouldReceive('getItem')->withArgs(['some_item'])->andReturn($this->mockItems[0]);
        $this->assertEquals('some_value', $this->adapter->get('some_item'));
    }

    public function testFetchMiss()
    {
        $this->mockItems[0]->shouldReceive('isHit')->times(1)->andReturn(false);
        $this->mockPool->shouldReceive('getItem')->withArgs(['no_item'])->andReturn($this->mockItems[0]);
        $this->assertFalse($this->adapter->get('no_item', false));
    }

    public function testFetchInvalid()
    {
        $invalidKeyString = '{}()/\@';

        $this->mockPool->shouldReceive('getItem')->withArgs([$invalidKeyString])->andReturn(false);
        $this->assertFalse($this->adapter->get($invalidKeyString, false));
    }

    public function testFetchMultiple()
    {
        $expectedResponse = [
            'first_item'  => 'first_value',
            'second_item' => 'second_value'
        ];

        $this->mockItems[0]->shouldReceive('isHit')->once()->andReturn(true);
        $this->mockItems[0]->shouldReceive('get')->once()->andReturn(array_values($expectedResponse)[0]);
        $this->mockItems[1]->shouldReceive('isHit')->once()->andReturn(true);
        $this->mockItems[1]->shouldReceive('get')->once()->andReturn(array_values($expectedResponse)[1]);

        $this->mockPool->shouldReceive('getItems')->withArgs([array_keys($expectedResponse)])->andReturn([ $this->mockItems[0], $this->mockItems[1] ]);

        $this->assertEquals(array_values($expectedResponse), $this->adapter->getMultiple(array_keys($expectedResponse)));
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
        $this->mockItems[0]->shouldReceive('set')->twice()->with('dummy_data');
        $this->mockItems[0]->shouldReceive('expiresAfter')->once()->with(null);
        $this->mockItems[0]->shouldReceive('expiresAfter')->once()->with(2);
        $this->mockPool->shouldReceive('getItem')->twice()->with('some_item')->andReturn($this->mockItems[0]);
        $this->mockPool->shouldReceive('save')->twice()->with($this->mockItems[0])->andReturn(true);
        $this->assertTrue($this->adapter->set('some_item', 'dummy_data'));
        $this->assertTrue($this->adapter->set('some_item', 'dummy_data', 2));
    }

    public function testSaveInvalid()
    {
        $invalidKeyString = '{}()/\@';

        $this->mockPool->shouldReceive('getItem')->once()->with($invalidKeyString);
        $this->assertFalse($this->adapter->set($invalidKeyString, 'dummy_data'));
    }

    public function testDelete()
    {
        $this->mockPool->shouldReceive('deleteItem')->once()->with('some_item')->andReturn(true);
        $this->assertTrue($this->adapter->delete('some_item'));
    }

    public function testDeleteInvalid()
    {
        $invalidKeyString = '{}()/\@';

        $this->mockPool->shouldReceive('deleteItem')->once()->with($invalidKeyString)->andReturn(false);
        $this->assertFalse($this->adapter->delete($invalidKeyString));
    }

    public function testClear()
    {
        $this->mockPool->shouldReceive('clear')->once()->andReturn(true);
        $this->assertTrue($this->adapter->clear());
    }
}
