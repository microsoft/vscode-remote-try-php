<?php

/**
 * Part of the Joomla Framework Session Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session;

/**
 * Interface defining a Joomla! session storage object
 *
 * @since  2.0.0
 */
interface StorageInterface
{
    /**
     * Get the session name
     *
     * @return  string  The session name
     *
     * @since   2.0.0
     */
    public function getName(): string;

    /**
     * Set the session name
     *
     * @param   string  $name  The session name
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function setName(string $name);

    /**
     * Get the session ID
     *
     * @return  string  The session ID
     *
     * @since   2.0.0
     */
    public function getId(): string;

    /**
     * Set the session ID
     *
     * @param   string  $id  The session ID
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function setId(string $id);

    /**
     * Check if the session is active
     *
     * @return  boolean
     *
     * @since   2.0.0
     */
    public function isActive(): bool;

    /**
     * Check if the session is started
     *
     * @return  boolean
     *
     * @since   2.0.0
     */
    public function isStarted(): bool;

    /**
     * Get data from the session store
     *
     * @param   string  $name     Name of a variable
     * @param   mixed   $default  Default value of a variable if not set
     *
     * @return  mixed  Value of a variable
     *
     * @since   2.0.0
     */
    public function get(string $name, $default);

    /**
     * Set data into the session store
     *
     * @param   string  $name   Name of a variable
     * @param   mixed   $value  Value of a variable
     *
     * @return  mixed  Old value of a variable
     *
     * @since   2.0.0
     */
    public function set(string $name, $value);

    /**
     * Check whether data exists in the session store
     *
     * @param   string  $name  Name of variable
     *
     * @return  boolean
     *
     * @since   2.0.0
     */
    public function has(string $name): bool;

    /**
     * Unset a variable from the session store
     *
     * @param   string  $name  Name of variable
     *
     * @return  mixed   The value from session or NULL if not set
     *
     * @since   2.0.0
     */
    public function remove(string $name);

    /**
     * Clears all variables from the session store
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function clear(): void;

    /**
     * Retrieves all variables from the session store
     *
     * @return  array
     *
     * @since   2.0.0
     */
    public function all(): array;

    /**
     * Start a session
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function start(): void;

    /**
     * Regenerates the session ID that represents this storage.
     *
     * This method must invoke session_regenerate_id($destroy) unless this interface is used for a storage object designed for unit
     * or functional testing where a real PHP session would interfere with testing.
     *
     * @param   boolean  $destroy  Destroy session when regenerating?
     *
     * @return  boolean  True on success
     *
     * @see     session_regenerate_id()
     * @since   2.0.0
     */
    public function regenerate(bool $destroy = false): bool;

    /**
     * Writes session data and ends session
     *
     * @return  void
     *
     * @see     session_write_close()
     * @since   2.0.0
     */
    public function close(): void;

    /**
     * Perform session data garbage collection
     *
     * @return  integer|boolean  Number of deleted sessions on success or boolean false on failure or if the function is unsupported
     *
     * @see     session_gc()
     * @since   2.0.0
     */
    public function gc();

    /**
     * Aborts the current session
     *
     * @return  boolean
     *
     * @see     session_abort()
     * @since   2.0.0
     */
    public function abort(): bool;
}
