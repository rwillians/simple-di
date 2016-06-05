<?php

namespace Rwillians\Container;

use Mockery;
use Rwillians\Container\Contracts\ReadableContainerInterface;
use Rwillians\Container\Contracts\ServiceProviderInterface;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Rwillians\Container\Exceptions\NotFoundException
     */
    public function testThrowsNotFoundExceptionWhenGivenIdIsNotSet()
    {
        $container = new Container;
        $container->get('undefined');
    }

    public function testGetValuesFromAGivenId()
    {
        $container = new Container([
            'foo' => 'bar',
        ]);

        $this->assertEquals('bar', $container->get('foo'));
    }

    /**
     * @return void
     */
    public function testShouldReceivedAnReadableContainerInterfaceWhenRegisteringAFactory()
    {
        $container = new Container;

        $container->set('factory', function ($container) {
            $this->assertInstanceOf(ReadableContainerInterface::class, $container);
        });

        $container->get('factory');
    }

    public function testCanRegisterAFactory()
    {
        $container = new Container([
            'foo' => 'bar',
        ]);

        $container->set('foobar', function ($container) {
            return sprintf('foo%s', $container->get('foo'));
        });

        $this->assertEquals('foobar', $container->get('foobar'));
    }

    public function testCanProtectAnCallableValueFromBeenInterpretedAsAnFactory()
    {
        $container = new Container;

        $container->set('sum', $container->protect(function ($total, $current) {
            return $total + $current;
        }));

        $sum = $container->get('sum');

        $this->assertInternalType('callable', $sum);
        $this->assertEquals(6, array_reduce(range(1,3), $sum));
    }

    public function testResolvingAFactoryShouldAlwaysReturnANewInstanceOfConcreteObject()
    {
        $container = new Container;

        $container->set('object', function () {
            return new \stdClass;
        });

        $this->assertNotSame($container->get('object'), $container->get('object'));
    }

    public function testResolvingASharedFactoryShouldAlwaysReturnTheSameInstaceOfConcreteObject()
    {
        $container = new Container;

        $container->set('object', $container->share(function () {
            return new \stdClass;
        }));

        $this->assertSame($container->get('object'), $container->get('object'));
    }

    public function testCanExtendAnyUnprotectedService()
    {
        $container = new Container;

        $container->set('foo', $container->share(function () {
            return new \stdClass();
        }));

        $container->extend('foo', function ($foo) {
            $foo->name = 'foo';

            return (array) $foo;
        });

        $this->assertEquals(['name' => 'foo'], $container->get('foo'));
    }

    /**
     * @expectedException \Rwillians\Container\Exceptions\UnableToModifyProtectedServiceException
     */
    public function testCanNotExtendAProtectedService()
    {
        $container = new Container;

        $container->set('protected', $container->protect(function () {
            return 'foobar';
        }));

        $container->extend('protected', function () {});
    }

    public function testCanRegisterAServiceProviderClass()
    {
        $container = new Container;

        $serviceProvider = Mockery::mock(ServiceProviderInterface::class);
        $serviceProvider->shouldReceive('register')->with($container)->andReturnUndefined();

        $container->register($serviceProvider);

        $serviceProvider->shouldHaveReceived('register')->once()->with($container);
    }
}
