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
 * Redis session storage handler
 *
 * @since  2.0.0
 */
class RedisHandler implements HandlerInterface
{
    /**
     * Session ID prefix to avoid naming conflicts
     *
     * @var    string
     * @since  2.0.0
     */
    private $prefix;

    /**
     * Redis driver
     *
     * @var    \Redis
     * @since  2.0.0
     */
    private $redis;

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
     * @param   \Redis  $redis    A Redis instance
     * @param   array   $options  Associative array of options to configure the handler
     *
     * @since   2.0.0
     */
    public function __construct(\Redis $redis, array $options = [])
    {
        $this->redis = $redis;

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
        // No need to close the connection to Redis server manually.
        return true;
    }

    /**
     * Destroy a session, called automatically when running session_regenerate_id().
     *
     * @param   integer  $session_id  The session ID being destroyed
     *
     * @return  boolean  True on success, false otherwise
     *
     * @since   2.0.0
     */
    public function destroy(string $id): bool
    {
        $this->redis->del($this->prefix . $id);

        // Session callback must have a return value of type bool when session_regenerate_id() is called.
        return true;
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
        return \extension_loaded('redis') && class_exists('Redis');
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
        return $this->redis->get($this->prefix . $session_id) ?: '';
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
        if ($this->ttl > 0) {
            return $this->redis->setex($this->prefix . $session_id, $this->ttl, $session_data);
        }

        return $this->redis->set($this->prefix . $session_id, $session_data);
    }
}
