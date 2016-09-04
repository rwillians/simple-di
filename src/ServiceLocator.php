<?php

namespace Rwillians\SimpleDI;

use Rwillians\SimpleDI\Contracts\ContainerInterface;
use Rwillians\SimpleDI\Contracts\ServiceLocatorInterface;

/**
 * Class ServiceLocator
 * @package Rwillians\SimpleDI
 * @author Rafael Willians <me@rwillians.com>
 */
class ServiceLocator implements ServiceLocatorInterface
{
    /**
     * @var \Rwillians\SimpleDI\Contracts\ContainerInterface
     */
    protected $container;

    /**
     * ServiceLocator constructor.
     * @param \Rwillians\SimpleDI\Contracts\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->container->resolve($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool
    {
        return $this->container->has($key);
    }
}