<?php

namespace Rwillians\SimpleDI\Contracts;

/**
 * Interface ContainerInterface
 * @package Rwillians\SimpleDI\Contracts
 * @author Rafael Willians <me@rwillians.com>
 */
interface ContainerInterface
{
    /**
     * @param \Closure $closure
     * @return \Closure
     */
    public function factory(\Closure $closure);

    /**
     * @param string $key
     * @return void
     */
    public function forget(string $key);

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool;

    /**
     * @return array|string[]
     */
    public function keys() : array;

    /**
     * @param \Closure $closure
     * @return \Closure
     */
    public function protect(\Closure $closure);

    /**
     * @param \Rwillians\SimpleDI\Contracts\ServiceProviderInterface $serviceProvider
     * @return void
     */
    public function register(ServiceProviderInterface $serviceProvider);

    /**
     * @param string $key
     * @return mixed
     */
    public function resolve(string $key);

    /**
     * @return \Rwillians\SimpleDI\Contracts\ServiceLocatorInterface
     */
    public function serviceLocator() : ServiceLocatorInterface;

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value);
}