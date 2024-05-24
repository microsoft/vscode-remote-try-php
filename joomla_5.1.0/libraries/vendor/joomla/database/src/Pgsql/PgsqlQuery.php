<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Pgsql;

use Joomla\Database\Pdo\PdoQuery;
use Joomla\Database\Query\PostgresqlQueryBuilder;
use Joomla\Database\Query\QueryElement;

/**
 * PDO PostgreSQL Query Building Class.
 *
 * @since  1.0
 *
 * @property-read  QueryElement  $forUpdate  The FOR UPDATE element used in "FOR UPDATE" lock
 * @property-read  QueryElement  $forShare   The FOR SHARE element used in "FOR SHARE" lock
 * @property-read  QueryElement  $noWait     The NOWAIT element used in "FOR SHARE" and "FOR UPDATE" lock
 * @property-read  QueryElement  $returning  The RETURNING element of INSERT INTO
 */
class PgsqlQuery extends PdoQuery
{
    use PostgresqlQueryBuilder;

    /**
     * The list of zero or null representation of a datetime.
     *
     * @var    array
     * @since  2.0.0
     */
    protected $nullDatetimeList = ['1970-01-01 00:00:00'];

    /**
     * Casts a value to a char.
     *
     * Ensure that the value is properly quoted before passing to the method.
     *
     * Usage:
     * $query->select($query->castAsChar('a'));
     * $query->select($query->castAsChar('a', 40));
     *
     * @param   string  $value   The value to cast as a char.
     * @param   string  $length  The length of the char.
     *
     * @return  string  Returns the cast value.
     *
     * @since   1.8.0
     */
    public function castAsChar($value, $length = null)
    {
        if ((int) $length < 1) {
            return $value . '::text';
        }

        return 'CAST(' . $value . ' AS CHAR(' . $length . '))';
    }
}
