<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Event;

use Joomla\Database\DatabaseInterface;
use Joomla\Event\Event;

/**
 * Database connection event
 *
 * @since  2.0.0
 */
class ConnectionEvent extends Event
{
    /**
     * DatabaseInterface object for this event
     *
     * @var    DatabaseInterface
     * @since  2.0.0
     */
    private $driver;

    /**
     * Constructor.
     *
     * @param   string             $name    The event name.
     * @param   DatabaseInterface  $driver  The DatabaseInterface object for this event.
     *
     * @since   2.0.0
     */
    public function __construct(string $name, DatabaseInterface $driver)
    {
        parent::__construct($name);

        $this->driver = $driver;
    }

    /**
     * Retrieve the DatabaseInterface object attached to this event.
     *
     * @return  DatabaseInterface
     *
     * @since   2.0.0
     */
    public function getDriver(): DatabaseInterface
    {
        return $this->driver;
    }
}
