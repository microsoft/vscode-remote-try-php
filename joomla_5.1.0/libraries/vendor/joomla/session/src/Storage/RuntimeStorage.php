<?php

/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session\Storage;

use Joomla\Session\StorageInterface;

/**
 * Session storage object that stores objects in Runtime memory. This is designed for use in CLI Apps, including
 * unit testing applications in PHPUnit.
 *
 * @since  2.0.0
 */
class RuntimeStorage implements StorageInterface
{
    /**
     * Flag if the session is active
     *
     * @var    boolean
     * @since  2.0.0
     */
    private $active = false;

    /**
     * Internal flag identifying whether the session has been closed
     *
     * @var    boolean
     * @since  2.0.0
     */
    private $closed = false;

    /**
     * Internal data store
     *
     * @var    array
     * @since  2.0.0
     */
    private $data = [];

    /**
     * Session ID
     *
     * @var    string
     * @since  2.0.0
     */
    private $id = '';

    /**
     * Session Name
     *
     * @var    string
     * @since  2.0.0
     */
    private $name = 'MockSession';

    /**
     * Internal flag identifying whether the session has been started
     *
     * @var    boolean
     * @since  2.0.0
     */
    private $started = false;

    /**
     * Retrieves all variables from the session store
     *
     * @return  array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Clears all variables from the session store
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * Writes session data and ends session
     *
     * @return  void
     *
     * @see     session_write_close()
     * @since   2.0.0
     */
    public function close(): void
    {
        $this->closed  = true;
        $this->started = false;
    }

    /**
     * Perform session data garbage collection
     *
     * @return  integer|boolean  Number of deleted sessions on success or boolean false on failure or if the function is unsupported
     *
     * @see     session_gc()
     * @since   2.0.0
     */
    public function gc()
    {
        return 0;
    }

    /**
     * Aborts the current session
     *
     * @return  boolean
     *
     * @see     session_abort()
     * @since   2.0.0
     */
    public function abort(): bool
    {
        $this->closed  = true;
        $this->started = false;

        return true;
    }

    /**
     * Generates a session ID
     *
     * @return  string
     *
     * @since   2.0.0
     */
    private function generateId(): string
    {
        return hash('sha256', uniqid(mt_rand()));
    }

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
    public function get(string $name, $default)
    {
        if (!$this->isStarted()) {
            $this->start();
        }

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return $default;
    }

    /**
     * Get the session ID
     *
     * @return  string  The session ID
     *
     * @since   2.0.0
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the session name
     *
     * @return  string  The session name
     *
     * @since   2.0.0
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Check whether data exists in the session store
     *
     * @param   string  $name  Name of variable
     *
     * @return  boolean
     *
     * @since   2.0.0
     */
    public function has(string $name): bool
    {
        if (!$this->isStarted()) {
            $this->start();
        }

        return isset($this->data[$name]);
    }

    /**
     * Check if the session is active
     *
     * @return  boolean
     *
     * @since   2.0.0
     */
    public function isActive(): bool
    {
        return $this->active = $this->started;
    }

    /**
     * Check if the session is started
     *
     * @return  boolean
     *
     * @since   2.0.0
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * Unset a variable from the session store
     *
     * @param   string  $name  Name of variable
     *
     * @return  mixed  The value from session or NULL if not set
     *
     * @since   2.0.0
     */
    public function remove(string $name)
    {
        if (!$this->isStarted()) {
            $this->start();
        }

        $old = $this->data[$name] ?? null;

        unset($this->data[$name]);

        return $old;
    }

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
    public function regenerate(bool $destroy = false): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($destroy) {
            $this->id = $this->generateId();
        }

        return true;
    }

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
    public function set(string $name, $value = null)
    {
        if (!$this->isStarted()) {
            $this->start();
        }

        $old = $this->data[$name] ?? null;

        $this->data[$name] = $value;

        return $old;
    }

    /**
     * Set the session ID
     *
     * @param   string  $id  The session ID
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  \LogicException
     */
    public function setId(string $id)
    {
        if ($this->isActive()) {
            throw new \LogicException('Cannot change the ID of an active session');
        }

        $this->id = $id;

        return $this;
    }

    /**
     * Set the session name
     *
     * @param   string  $name  The session name
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  \LogicException
     */
    public function setName(string $name)
    {
        if ($this->isActive()) {
            throw new \LogicException('Cannot change the name of an active session');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Start a session
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function start(): void
    {
        if ($this->isStarted()) {
            return;
        }

        if ($this->isActive()) {
            throw new \RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (empty($this->id)) {
            $this->setId($this->generateId());
        }

        $this->closed  = false;
        $this->started = true;
        $this->isActive();
    }
}
