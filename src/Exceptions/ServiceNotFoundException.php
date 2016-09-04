<?php

namespace Rwillians\SimpleDI\Exceptions;

/**
 * Class ServiceNotFoundException
 * @package Rwillians\SimpleDI\Exceptions
 * @author Rafael Willians <me@rwillians.com>
 */
class ServiceNotFoundException extends \LogicException
{
    /**
     * ServiceNotFoundException constructor.
     * @param string $key
     */
    public function __construct(string $key)
    {
        parent::__construct(sprintf('Unable to find service registered as "%s".', $key));
    }
}