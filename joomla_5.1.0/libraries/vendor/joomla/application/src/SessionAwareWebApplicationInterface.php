<?php

/**
 * Part of the Joomla Framework Application Package
 *
 * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application;

use Joomla\Session\SessionInterface;

/**
 * Application sub-interface defining a web application class which supports sessions
 *
 * @since  2.0.0
 */
interface SessionAwareWebApplicationInterface extends WebApplicationInterface
{
    /**
     * Method to get the application session object.
     *
     * @return  SessionInterface  The session object
     *
     * @since   2.0.0
     */
    public function getSession();

    /**
     * Sets the session for the application to use, if required.
     *
     * @param   SessionInterface  $session  A session object.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function setSession(SessionInterface $session);

    /**
     * Checks for a form token in the request.
     *
     * @param   string  $method  The request method in which to look for the token key.
     *
     * @return  boolean
     *
     * @since   2.0.0
     */
    public function checkToken($method = 'post');

    /**
     * Method to determine a hash for anti-spoofing variable names
     *
     * @param   boolean  $forceNew  If true, force a new token to be created
     *
     * @return  string  Hashed var name
     *
     * @since   2.0.0
     */
    public function getFormToken($forceNew = false);
}
