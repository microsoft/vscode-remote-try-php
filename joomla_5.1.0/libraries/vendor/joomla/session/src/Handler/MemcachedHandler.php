<?php

/**
 * Part of the Joomla Framework Session Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session\Handler;

use Joomla\Session\HandlerInterface;

/**
 * Memcached session storage handler
 *
 * @since  2.0.0
 */
class MemcachedHandler implements HandlerInterface
{
    /**
     * Memcached driver
     *
     * @var    \Memcached
     * @since  2.0.0
     */
    private $memcached;

    /**
     * Session ID prefix to avoid naming conflicts
     *
     * @var    string
     * @since  2.0.0
     */
    private $prefix;

    /**
     * Time to live in seconds
     *
     * @var    integer
     * @since  2.0.0
     */
    private $ttl;

    /**
     * Constructor
     *
     * @param   \Memcached  $memcached  A Memcached instance
     * @param   array       $options    Associative array of options to configure the handler
     *
     * @since   2.0.0
     */
    public function __construct(\Memcached $memcached, array $options = [])
    {
        $this->memcached = $memcached;

        // Set the default time-to-live based on the Session object's default configuration
        $this->ttl = isset($options['ttl']) ? (int) $options['ttl'] : 900;

        // Namespace our session IDs to avoid potential conflicts
        $this->prefix = $options['prefix'] ?? 'jfw';
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
        return $this->memcached->delete($this->prefix . $id);
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
        // Memcached manages garbage collection on its own
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
        /*
         * GAE and HHVM have both had instances where Memcached the class was defined but no extension was loaded.
         * If the class is there, we can assume it works.
         */
        return class_exists('Memcached');
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
        return $this->memcached->get($this->prefix . $session_id) ?: '';
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
        return $this->memcached->set($this->prefix . $session_id, $session_data, time() + $this->ttl);
    }
}
