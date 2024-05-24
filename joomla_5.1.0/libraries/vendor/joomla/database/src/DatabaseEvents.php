<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

/**
 * Class defining the events dispatched by the database API
 *
 * @since  2.0.0
 */
final class DatabaseEvents
{
    /**
     * Private constructor to prevent instantiation of this class
     *
     * @since   2.0.0
     */
    private function __construct()
    {
    }

    /**
     * Database event which is dispatched after the connection to the database server is opened.
     *
     * Listeners to this event receive a `Joomla\Database\Event\ConnectionEvent` object.
     *
     * @var    string
     * @since  2.0.0
     */
    public const POST_CONNECT = 'onAfterConnect';

    /**
     * Database event which is dispatched after the connection to the database server is closed.
     *
     * Listeners to this event receive a `Joomla\Database\Event\ConnectionEvent` object.
     *
     * @var    string
     * @since  2.0.0
     */
    public const POST_DISCONNECT = 'onAfterDisconnect';
}
