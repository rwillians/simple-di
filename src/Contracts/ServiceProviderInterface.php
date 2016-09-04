<?php

namespace Rwillians\SimpleDI\Contracts;

/**
 * Interface ServiceProviderInterface
 * @package Rwillians\SimpleDI\Contracts
 * @author Rafael Willians <me@rwillians.com>
 */
interface ServiceProviderInterface
{
    /**
     * @param \Rwillians\SimpleDI\Contracts\ContainerInterface $container
     * @return void
     */
    public function register(ContainerInterface $container);
}