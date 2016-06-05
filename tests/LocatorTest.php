<?php

namespace Rwillians\Container;

use Mockery;
use Rwillians\Container\Contracts\ReadableContainerInterface;

class LocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCheckIfHasAServiceByItsId()
    {
        $container = Mockery::mock(ReadableContainerInterface::class);
        $container->shouldReceive('has')->with('foo')->andReturn(true);

        $serviceLocator = new ServiceLocator($container);

        $this->assertTrue($serviceLocator->has('foo'));
    }

    public function testCanGetAServiceByItsId()
    {
        $container = Mockery::mock(ReadableContainerInterface::class);
        $container->shouldReceive('get')->with('foo')->andReturn('bar');

        $serviceLocator = new ServiceLocator($container);

        $this->assertEquals('bar', $serviceLocator->get('foo'));
    }
}
