<?php

namespace Rwillians\Container\Contracts;

/**
 * @package Rwillians\Container\Contracts
 * @author Rafael Willians <me@rwillians.com>
 */
interface ServiceProviderInterface
{
    /**
     * @param \Rwillians\Container\Contracts\WritableContainerInterface $container
     * @return void
     */
    public function register(WritableContainerInterface $container);
}
