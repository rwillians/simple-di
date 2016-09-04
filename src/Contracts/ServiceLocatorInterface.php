<?php

namespace Rwillians\SimpleDI\Contracts;

/**
 * Interface ServiceLocatorInterface
 * @package Rwillians\SimpleDI\Contracts
 * @author Rafael Willians <me@rwillians.com>
 */
interface ServiceLocatorInterface
{
    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool;
}