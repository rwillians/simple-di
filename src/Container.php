<?php

namespace Rwillians\Container;

use Rwillians\Container\Contracts\ReadableContainerInterface;
use Rwillians\Container\Contracts\ServiceProviderInterface;
use Rwillians\Container\Contracts\WritableContainerInterface;
use Rwillians\Container\Exceptions\NotFoundException;
use Rwillians\Container\Exceptions\UnableToModifyProtectedServiceException;

/**
 * @package Rwillians\Container
 * @author Rafael Willians <me@rwillians.com>
 */
class Container implements WritableContainerInterface
{
    /**
     * @var \Rwillians\Container\ServiceLocator
     */
    protected $locator;

    /**
     * @var \SplObjectStorage
     */
    protected $protected;

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->protected = new \SplObjectStorage();
        $this->load($values);
    }

    /**
     * @param string $id
     * @param callable $callable
     * @throws \Rwillians\Container\Exceptions\NotFoundException
     * @throws \Rwillians\Container\Exceptions\UnableToModifyProtectedServiceException
     * @return mixed
     */
    public function extend($id, callable $callable)
    {
        $service = $this->get($id);

        if (is_callable($service) && $this->protected->offsetExists($service)) {
            throw new UnableToModifyProtectedServiceException();
        }

        $extended = call_user_func($callable, $service, $this->locator());

        if ($service !== $extended) {
            $this->set($id, $extended);
        }

        return $extended;
    }

    /**
     * @param string $id
     * @throws \Rwillians\Container\Exceptions\NotFoundException
     * @return mixed
     */
    public function get($id)
    {
        if (! $this->has($id)) {
            throw new NotFoundException;
        }

        return $this->resolve($this->services[$id]);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->services);
    }

    /**
     * @return \Rwillians\Container\ServiceLocator
     */
    public function locator()
    {
        return $this->locator ?: $this->locator = new ServiceLocator($this);
    }

    /**
     * @param \Closure $factory
     * @return callable
     */
    public function protect(\Closure $factory)
    {
        $this->protected->attach($factory);

        return function () use ($factory) {
            return $factory;
        };
    }

    /**
     * @param \Rwillians\Container\Contracts\ServiceProviderInterface $serviceProvider
     * @param array $values
     */
    public function register(ServiceProviderInterface $serviceProvider, array $values = [])
    {
        $serviceProvider->register($this);
        $this->load($values);
    }

    /**
     * @param string $id
     * @param mixed $value
     */
    public function set($id, $value)
    {
        $this->services[$id] = $value;
    }

    /**
     * @param \Closure $factory
     * @return mixed
     */
    public function share(\Closure $factory)
    {
        return function (ReadableContainerInterface $readableContainer) use ($factory) {
            static $shared;

            if (null === $shared) {
                $shared = call_user_func($factory, $readableContainer);
            }

            return $shared;
        };
    }

    /**
     * @param array $values
     */
    protected function load(array $values)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function resolve($value)
    {
        if (is_callable($value)) {
            return call_user_func($value, $this->locator());
        }

        return $value;
    }
}
