<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

/**
 * Interface defining a driver which has support for the MySQL `utf8mb4` character set
 *
 * @since  2.0.0
 */
interface UTF8MB4SupportInterface
{
    /**
     * Automatically downgrade a CREATE TABLE or ALTER TABLE query from utf8mb4 (UTF-8 Multibyte) to plain utf8.
     *
     * Used when the server doesn't support UTF-8 Multibyte.
     *
     * @param   string  $query  The query to convert
     *
     * @return  string  The converted query
     *
     * @since   2.0.0
     */
    public function convertUtf8mb4QueryToUtf8($query);

    /**
     * Check whether the database engine supports the UTF-8 Multibyte (utf8mb4) character encoding.
     *
     * @return  boolean  True if the database engine supports UTF-8 Multibyte.
     *
     * @since   2.0.0
     */
    public function hasUtf8mb4Support();
}
