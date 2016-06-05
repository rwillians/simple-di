<?php

namespace Rwillians\Container\Contracts;

/**
 * @package Rwillians\Container\Contracts
 * @author Rafael Willians <me@rwillians.com>
 */
interface WritableContainerInterface extends ReadableContainerInterface
{
    /**
     * @param string $id
     * @param mixed $value
     */
    public function set($id, $value);

    /**
     * @param string $id
     * @param callable $callable
     * @return void
     */
    public function extend($id, callable $callable);

    /**
     * @param \Closure $factory
     * @return mixed
     */
    public function protect(\Closure $factory);

    /**
     * @param \Closure $factory
     * @return mixed
     */
    public function share(\Closure $factory);

    /**
     * @param \Rwillians\Container\Contracts\ServiceProviderInterface $serviceProvider
     * @param array $values
     */
    public function register(ServiceProviderInterface $serviceProvider, array $values = []);
}
