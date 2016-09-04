<?php

namespace Rwillians\SimpleDI\Exceptions;

/**
 * Class OverridingFrozenServiceException
 * @package Rwillians\SimpleDI\Exceptions
 * @author Rafael Willians <me@rwillians.com>
 */
class OverridingFrozenServiceException extends \LogicException
{
    /**
     * OverridingFrozenServiceException constructor.
     * @param string $key
     */
    public function __construct(string $key)
    {
        parent::__construct(sprintf('You\'re not allowed to override the frozen service "%s"', $key));
    }
}