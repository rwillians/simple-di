<?php

namespace Rwillians\SimpleDI;

use Mockery as M;
use Rwillians\SimpleDI\Contracts\ContainerInterface;

/**
 * Class ServiceLocatorTest
 * @package Rwillians\SimpleDI
 * @author Rafael Willians <me@rwillians.com>
 */
class ServiceLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCheckIfContainerHasAServiceRegistered()
    {
        $container = M::mock(ContainerInterface::class)
            ->shouldReceive('has')
            ->with($key = 'foo')
            ->andReturn(false)
            ->getMock();

        $serviceLocator = new ServiceLocator($container);

        $this->assertFalse($serviceLocator->has($key));
    }

    public function testItCanGetAValueFromAContainer()
    {
        $container = M::mock(ContainerInterface::class)
            ->shouldReceive('resolve')
            ->with($key = 'foo')
            ->andReturn($expected = 'bar')
            ->getMock();

        $serviceLocator = new ServiceLocator($container);

        $this->assertEquals($expected, $serviceLocator->get($key));
    }
}
