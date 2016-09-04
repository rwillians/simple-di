<?php

namespace Rwillians\SimpleDI;

use Mockery as M;
use Rwillians\SimpleDI\Contracts\ContainerInterface;
use Rwillians\SimpleDI\Contracts\ServiceLocatorInterface;
use Rwillians\SimpleDI\Contracts\ServiceProviderInterface;

/**
 * Class ContainerTest
 * @package Rwillians\SimpleDI
 * @author Rafael Willians <me@rwillians.com>
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testItsAnInstanceOfContainerInterface()
    {
        $container = new Container();

        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testItCanCheckIfHasKey()
    {
        $container = new Container();
        $container->set($key = 'foo', 'bar');

        $this->assertTrue($container->has($key));
        $this->assertFalse($container->has(uniqid()));
    }

    public function testItCanSetAndResolveARawValue()
    {
        $container = new Container();
        $container->set($key = 'foo', $expected = 'bar');

        $this->assertEquals($expected, $container->resolve($key));
    }

    public function testItCanSetAndResolveAService()
    {
        $container = new Container();
        $container->set($dependency = 'foo', $dependencyValue = 1);
        $container->set($key = 'bar', function (ServiceLocatorInterface $serviceLocator) use ($dependency) {
            return $serviceLocator->get($dependency) + 1;
        });

        $this->assertEquals($dependencyValue + 1, $container->resolve($key));
    }

    public function testResolvingAServiceMoreThanOnceReturnsTheSameInstance()
    {
        $container = new Container();
        $container->set($key = 'shared', function () {
            return new \SplObjectStorage();
        });

        $expected = $container->resolve($key);

        $this->assertInstanceOf(\SplObjectStorage::class, $expected);
        $this->assertSame($expected, $container->resolve($key));
    }

    public function testResolvingAFactoredServicesReturnsANewInstanceForEachResolution()
    {
        $container = new Container();
        $container->set($key = 'factored', $factory = $container->factory(function () {
            return new \SplObjectStorage();
        }));

        $this->assertInstanceOf(\Closure::class, $factory);

        $firstResolution = $container->resolve($key);
        $secondResolution = $container->resolve($key);

        foreach ([$firstResolution, $secondResolution] as $resolution) {
            $this->assertInstanceOf(\SplObjectStorage::class, $resolution);
        }

        $this->assertEquals($firstResolution, $secondResolution);
        $this->assertNotSame($firstResolution, $secondResolution);
    }

    public function testItCanSetAProtectedValueSoItWillAlwaysBeReturnedAsItsRaw()
    {
        $container = new Container();
        $container->set($key = 'protected', $container->protect(function() {
            return 1;
        }));

        $this->assertInstanceOf(\Closure::class, $container->resolve($key));
    }

    public function testItCanBeInstantiatedWithValues()
    {
        $container = new Container([
            'foo' => $expected = 'bar',
            'bar' => function (ServiceLocatorInterface $serviceLocator) {
                return strrev($serviceLocator->get('foo'));
            },
        ]);

        $this->assertEquals($expected, $container->resolve('foo'));
        $this->assertEquals(strrev($expected), $container->resolve('bar'));
    }

    public function testItCanProvideAnArrayOfRegisteredServices()
    {
        $container = new Container($values = [
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        $this->assertEquals(array_keys($values), $container->keys());
    }

    /**
     * @expectedException \Rwillians\SimpleDI\Exceptions\ServiceNotFoundException
     */
    public function testItThrowsAnExceptionWhenTryingToResolveAnUnregisteredValue()
    {
        $container = new Container();
        $container->resolve('foo');
    }

    /**
     * @expectedException \Rwillians\SimpleDI\Exceptions\OverridingFrozenServiceException
     */
    public function testItDoesNotAllowYouToOverrideASharedServiceWhichHasAlreadyBeenResolved()
    {
        $container = new Container();
        $container->set($key = 'foo', function () {
            return new \SplObjectStorage();
        });

        $resolved = $container->resolve($key);

        $container->set($key, 'bar');
    }

    public function testItCanRegisterServiceProviders()
    {
        $serviceProvider = M::mock(ServiceProviderInterface::class)
            ->shouldReceive('register')
            ->with(ContainerInterface::class)
            ->andReturnUndefined()
            ->getMock();

        $container = new Container();
        $container->register($serviceProvider);

        $serviceProvider->shouldHaveReceived('register')->once();
    }

    public function testItCanHaveAServiceUnset()
    {
        $container = new Container([
            $key = 'foo' => 'bar',
        ]);

        $container->forget($key);

        $this->assertFalse($container->has($key));
    }

    /**
     * @expectedException \Rwillians\SimpleDI\Exceptions\ServiceNotFoundException
     */
    public function testTryingToRemovedAnNonExistentServiceThrowsAnException()
    {
        $container = new Container();
        $container->forget('foo');
    }

    public function testRemovingAFrozenServiceRemovesItsFrozenLockSoYouAreAbleToSetItAgain()
    {
        $container = new Container([
            $key = 'foo' => function () {
                return 'bar';
            },
        ]);

        $container->forget($key);
        $container->set($key, 'bar');

        $this->assertTrue($container->has($key));
    }
}
