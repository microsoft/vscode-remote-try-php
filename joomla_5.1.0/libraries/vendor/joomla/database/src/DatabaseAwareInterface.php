<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

/**
 * Defines the interface for a DatabaseInterface aware class.
 *
 * @since  2.1.0
 */
interface DatabaseAwareInterface
{
    /**
     * Set the database.
     *
     * @param   DatabaseInterface  $db  The database.
     *
     * @return  void
     *
     * @since   2.1.0
     */
    public function setDatabase(DatabaseInterface $db): void;
}
