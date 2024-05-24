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
 * APCu session storage handler
 *
 * @since  2.0.0
 */
class ApcuHandler implements HandlerInterface
{
    /**
     * Session ID prefix to avoid naming conflicts
     *
     * @var    string
     * @since  2.0.0
     */
    private $prefix;

    /**
     * Constructor
     *
     * @param   array  $options  Associative array of options to configure the handler
     *
     * @since   2.0.0
     */
    public function __construct(array $options = [])
    {
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
        // The apcu_delete function returns false if the id does not exist
        return apcu_delete($this->prefix . $id) || !apcu_exists($this->prefix . $id);
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
        $supported = \extension_loaded('apcu') && ini_get('apc.enabled');

        // If on the CLI interface, the `apc.enable_cli` option must also be enabled
        if ($supported && PHP_SAPI === 'cli') {
            $supported = ini_get('apc.enable_cli');
        }

        return (bool) $supported;
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
        return (string) apcu_fetch($this->prefix . $session_id);
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
        return apcu_store($this->prefix . $session_id, $session_data, ini_get('session.gc_maxlifetime'));
    }
}
