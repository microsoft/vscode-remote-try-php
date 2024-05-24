<?php

/**
 * Part of the Joomla Framework Session Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session\Handler;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\ParameterType;
use Joomla\Session\Exception\CreateSessionTableException;
use Joomla\Session\Exception\UnsupportedDatabaseDriverException;
use Joomla\Session\HandlerInterface;

/**
 * Database session storage handler
 *
 * @since  2.0.0
 */
class DatabaseHandler implements HandlerInterface
{
    /**
     * Database connector
     *
     * @var    DatabaseInterface
     * @since  2.0.0
     */
    private $db;

    /**
     * Flag whether gc() has been called
     *
     * @var    boolean
     * @since  2.0.0
     */
    private $gcCalled = false;

    /**
     * Lifetime for garbage collection
     *
     * @var    integer
     * @since  2.0.0
     */
    private $gcLifetime;

    /**
     * Constructor
     *
     * @param   DatabaseInterface  $db  Database connector
     *
     * @since   2.0.0
     */
    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    /**
     * Close the session
     *
     * @return  boolean  True on success, false otherwise
     *
     * @since   2.0.0
     */
    #[\ReturnTypeWillChange]
    public function close()
    {
        if ($this->gcCalled) {
            $query = $this->db->getQuery(true)
                ->delete($this->db->quoteName('#__session'))
                ->where($this->db->quoteName('time') . ' < ?')
                ->bind(1, $this->gcLifetime, ParameterType::INTEGER);

            // Remove expired sessions from the database.
            $this->db->setQuery($query)->execute();

            $this->gcCalled   = false;
            $this->gcLifetime = null;
        }

        $this->db->disconnect();

        return true;
    }

    /**
     * Creates the session database table
     *
     * @return  boolean
     *
     * @since   2.0.0
     * @throws  CreateSessionTableException
     * @throws  UnsupportedDatabaseDriverException
     */
    public function createDatabaseTable(): bool
    {
        switch ($this->db->getName()) {
            case 'mysql':
            case 'mysqli':
                $filename = 'mysql.sql';

                break;

            case 'postgresql':
                $filename = 'pgsql.sql';

                break;

            case 'sqlsrv':
            case 'sqlazure':
                $filename = 'sqlsrv.sql';

                break;

            case 'sqlite':
                $filename = 'sqlite.sql';

                break;

            default:
                throw new UnsupportedDatabaseDriverException(sprintf('The %s database driver is not supported.', $this->db->getName()));
        }

        $path = \dirname(__DIR__, 2) . '/meta/sql/' . $filename;

        if (!is_readable($path)) {
            throw new CreateSessionTableException(
                sprintf('Database schema could not be read from %s. Please ensure the file exists and is readable.', $path)
            );
        }

        $queries = DatabaseDriver::splitSql(file_get_contents($path));

        foreach ($queries as $query) {
            $query = trim($query);

            if ($query !== '') {
                try {
                    $this->db->setQuery($query)->execute();
                } catch (ExecutionFailureException $exception) {
                    throw new CreateSessionTableException('Failed to create the session table.', 0, $exception);
                }
            }
        }

        return true;
    }

    /**
     * Destroy a session
     *
     * @param   string  $session_id  The session ID being destroyed
     *
     * @return  boolean  True on success, false otherwise
     *
     * @since   2.0.0
     */
    public function destroy(string $id): bool
    {
        try {
            $query = $this->db->getQuery(true)
                ->delete($this->db->quoteName('#__session'))
                ->where($this->db->quoteName('session_id') . ' = ' . $this->db->quote($id));

            // Remove a session from the database.
            $this->db->setQuery($query)->execute();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cleanup old sessions
     *
     * @param   integer  $maxlifetime  Sessions that have not updated for the last maxlifetime seconds will be removed
     *
     * @return  boolean  True on success, false otherwise
     *
     * @since   2.0.0
     */
    #[\ReturnTypeWillChange]
    public function gc($maxlifetime)
    {
        // We'll delay garbage collection until the session is closed to prevent potential issues mid-cycle
        $this->gcLifetime = time() - $maxlifetime;
        $this->gcCalled   = true;

        return true;
    }

    /**
     * Test to see if the HandlerInterface is available
     *
     * @return  boolean  True on success, false otherwise
     *
     * @since   2.0.0
     */
    public static function isSupported(): bool
    {
        return interface_exists(DatabaseInterface::class);
    }

    /**
     * Initialize session
     *
     * @param   string  $save_path   The path where to store/retrieve the session
     * @param   string  $session_id  The session id
     *
     * @return  boolean  True on success, false otherwise
     *
     * @since   2.0.0
     */
    #[\ReturnTypeWillChange]
    public function open($save_path, $session_id)
    {
        $this->db->connect();

        return true;
    }

    /**
     * Read session data
     *
     * @param   string  $session_id  The session id to read data for
     *
     * @return  string  The session data
     *
     * @since   2.0.0
     */
    #[\ReturnTypeWillChange]
    public function read($session_id)
    {
        try {
            // Get the session data from the database table.
            $query = $this->db->getQuery(true)
                ->select($this->db->quoteName('data'))
                ->from($this->db->quoteName('#__session'))
                ->where($this->db->quoteName('session_id') . ' = ?')
                ->bind(1, $session_id);

            $this->db->setQuery($query);

            return (string) $this->db->loadResult();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Write session data
     *
     * @param   string  $session_id    The session id
     * @param   string  $session_data  The encoded session data
     *
     * @return  boolean  True on success, false otherwise
     *
     * @since   2.0.0
     */
    #[\ReturnTypeWillChange]
    public function write($session_id, $session_data)
    {
        try {
            // Figure out if a row exists for the session ID
            $query = $this->db->getQuery(true)
                ->select($this->db->quoteName('session_id'))
                ->from($this->db->quoteName('#__session'))
                ->where($this->db->quoteName('session_id') . ' = ?')
                ->bind(1, $session_id);

            $idExists = $this->db->setQuery($query)->loadResult();

            $query = $this->db->getQuery(true);

            $time = time();

            if ($idExists) {
                $query->update($this->db->quoteName('#__session'))
                    ->set($this->db->quoteName('data') . ' = ?')
                    ->set($this->db->quoteName('time') . ' = ?')
                    ->where($this->db->quoteName('session_id') . ' = ?')
                    ->bind(1, $session_data)
                    ->bind(2, $time, ParameterType::INTEGER)
                    ->bind(3, $session_id);
            } else {
                $query->insert($this->db->quoteName('#__session'))
                    ->columns([$this->db->quoteName('data'), $this->db->quoteName('time'), $this->db->quoteName('session_id')])
                    ->values('?, ?, ?')
                    ->bind(1, $session_data)
                    ->bind(2, $time, ParameterType::INTEGER)
                    ->bind(3, $session_id);
            }

            // Try to insert the session data in the database table.
            $this->db->setQuery($query)->execute();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
