<?php

namespace Rwillians\Container;

use Rwillians\Container\Contracts\ReadableContainerInterface;
use Rwillians\Container\Contracts\ServiceLocatorInterface;

/**
 * @package Rwillians\Container
 * @author Rafael Willians <me@rwillians.com>
 */
class ServiceLocator implements ServiceLocatorInterface
{
    /**
     * @var \Rwillians\Container\Contracts\ReadableContainerInterface
     */
    protected $container;

    /**
     * @param \Rwillians\Container\Contracts\ReadableContainerInterface $container
     */
    public function __construct(ReadableContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * @param string $id
     * @throws \Rwillians\Container\Exceptions\NotFoundException  No entry was found for this identifier.
     * @throws \Rwillians\Container\Exceptions\ContainerException Error while retrieving the entry.
     * @return mixed
     */
    public function get($id)
    {
        return $this->container->get($id);
    }
}
