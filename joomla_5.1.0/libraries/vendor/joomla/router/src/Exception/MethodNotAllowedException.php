<?php

/**
 * Part of the Joomla Framework Router Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Router\Exception;

/**
 * Exception defining a method not allowed error.
 *
 * @since  2.0.0
 */
class MethodNotAllowedException extends \RuntimeException
{
    /**
     * Allowed methods for the given route
     *
     * @var    string[]
     * @since  2.0.0
     */
    protected $allowedMethods = [];

    /**
     * Constructor.
     *
     * @param   array        $allowedMethods  The allowed methods for the route.
     * @param   null         $message         The Exception message to throw.
     * @param   integer      $code            The Exception code.
     * @param   ?\Exception  $previous        The previous throwable used for the exception chaining.
     */
    public function __construct(array $allowedMethods, $message = null, $code = 405, \Exception $previous = null)
    {
        $this->allowedMethods = array_map('strtoupper', $allowedMethods);

        parent::__construct($message, $code, $previous);
    }

    /**
     * Gets the allowed HTTP methods.
     *
     * @return  array
     *
     * @since  2.0.0
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}
