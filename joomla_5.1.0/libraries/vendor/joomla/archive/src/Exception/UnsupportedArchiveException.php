<?php

/**
 * Part of the Joomla Framework Archive Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Exception;

/**
 * Exception class defining an unsupported archive adapter
 *
 * @since  2.0.0
 */
class UnsupportedArchiveException extends \InvalidArgumentException
{
    /**
     * The unsupported archive adapter name
     *
     * @var    string
     * @since  2.0.0-beta2
     */
    protected $adapterType = '';

    /**
     * Constructor
     *
     * @param   string       $adapterType  The unsupported adapter type.
     * @param   string       $message      The Exception message to throw.
     * @param   int          $code         The Exception code.
     * @param   ?\Throwable  $previous     The previous throwable used for the exception chaining.
     *
     * @since  2.0.0-beta2
     */
    public function __construct(string $adapterType, string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->adapterType = $adapterType;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Gets the name of the adapter type that was unsupported
     *
     * @return  string
     *
     * @since  2.0.0-beta2
     */
    public function getUnsupportedAdapterType(): string
    {
        return $this->adapterType;
    }
}
