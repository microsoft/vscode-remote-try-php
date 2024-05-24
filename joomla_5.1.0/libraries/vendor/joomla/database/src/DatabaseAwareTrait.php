<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2022 Open Source Matters, Inc. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

use Joomla\Database\Exception\DatabaseNotFoundException;

/**
 * Defines the trait for a Database Aware Class.
 *
 * @since  2.1.0
 */
trait DatabaseAwareTrait
{
    /**
     * Database
     *
     * @var    DatabaseInterface
     * @since  2.1.0
     */
    private $databaseAwareTraitDatabase;

    /**
     * Get the database.
     *
     * @return  DatabaseInterface
     *
     * @since   2.1.0
     * @throws  DatabaseNotFoundException May be thrown if the database has not been set.
     */
    protected function getDatabase(): DatabaseInterface
    {
        if ($this->databaseAwareTraitDatabase) {
            return $this->databaseAwareTraitDatabase;
        }

        throw new DatabaseNotFoundException('Database not set in ' . \get_class($this));
    }

    /**
     * Set the database.
     *
     * @param   DatabaseInterface  $db  The database.
     *
     * @return  void
     *
     * @since   2.1.0
     */
    public function setDatabase(DatabaseInterface $db): void
    {
        $this->databaseAwareTraitDatabase = $db;
    }
}
