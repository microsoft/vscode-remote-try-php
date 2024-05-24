<?php

/**
 * Part of the Joomla Framework Session Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session;

/**
 * Interface defining Joomla! session handlers
 *
 * @since  2.0.0
 */
interface HandlerInterface extends \SessionHandlerInterface
{
    /**
     * Test to see if the HandlerInterface is available.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   2.0.0
     */
    public static function isSupported(): bool;
}
