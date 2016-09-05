<?php

namespace Rwillians\SimpleDI;

use Rwillians\SimpleDI\Contracts\ContainerInterface;
use Rwillians\SimpleDI\Contracts\ServiceLocatorInterface;
use Rwillians\SimpleDI\Contracts\ServiceProviderInterface;
use Rwillians\SimpleDI\Exceptions\OverridingFrozenServiceException;
use Rwillians\SimpleDI\Exceptions\ServiceNotFoundException;

/**
 * Class Container
 * @package Rwillians\SimpleDI
 * @author Rafael Willians <me@rwillians.com>
 */
class Container implements ContainerInterface
{
    /**
     * @var array
     */
    protected $frozen = [];

    /**
     * @var \SplObjectStorage
     */
    protected $protected;

    /**
     * @var \SplObjectStorage
     */
    protected $factories;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * Container constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->factories = new \SplObjectStorage();
        $this->protected = new \SplObjectStorage();

        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param \Closure $factory
     * @return \Closure
     */
    public function factory(\Closure $factory)
    {
        $this->factories->attach($factory);

        return $factory;
    }

    /**
     * @param string $key
     */
    public function forget(string $key)
    {
        if (! $this->has($key)) {
            throw new ServiceNotFoundException($key);
        }

        unset($this->frozen[$key]);
        unset($this->values[$key]);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * @return array|string[]
     */
    public function keys() : array
    {
        return array_keys($this->values);
    }

    /**
     * @param \Closure $closure
     * @return \Closure
     */
    public function protect(\Closure $closure)
    {
        $this->protected->attach($closure);

        return $closure;
    }

    /**
     * @param \Rwillians\SimpleDI\Contracts\ServiceProviderInterface $serviceProvider
     * @return void
     */
    public function register(ServiceProviderInterface $serviceProvider)
    {
        $serviceProvider->register($this);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function resolve(string $key)
    {
        if (! $this->has($key)) {
            throw new ServiceNotFoundException($key);
        }

        $value = $this->values[$key];

        if ($this->isProtected($value)) {
            return $value;
        }

        if ($this->isFactory($value)) {
            return call_user_func($value, $this->serviceLocator());
        }

        if ($value instanceof \Closure) {
            $this->frozen[$key] = true;

            return $this->values[$key] = call_user_func($value, $this->serviceLocator());
        }

        return $this->values[$key];
    }

    /**
     * @return \Rwillians\SimpleDI\Contracts\ServiceLocatorInterface
     */
    public function serviceLocator() : ServiceLocatorInterface
    {
        return new ServiceLocator($this);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value)
    {
        if (array_key_exists($key, $this->frozen)) {
            throw new OverridingFrozenServiceException($key);
        }

        $this->values[$key] = $value;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function isFactory($value) : bool
    {
        return is_object($value) && $this->factories->contains($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function isProtected($value) : bool
    {
        return is_object($value) && $this->protected->contains($value);
    }
}