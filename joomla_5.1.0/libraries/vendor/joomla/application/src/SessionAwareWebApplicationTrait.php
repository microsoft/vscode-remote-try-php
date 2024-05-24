<?php

/**
 * Part of the Joomla Framework Application Package
 *
 * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application;

use Joomla\Input\Input;
use Joomla\Session\SessionInterface;

/**
 * Trait which helps implementing `Joomla\Application\SessionAwareWebApplicationInterface` in a web application class.
 *
 * @since  2.0.0
 */
trait SessionAwareWebApplicationTrait
{
    /**
     * The application session object.
     *
     * @var    SessionInterface
     * @since  2.0.0
     */
    protected $session;

    /**
     * Method to get the application input object.
     *
     * @return  Input
     *
     * @since   2.0.0
     */
    abstract public function getInput(): Input;

    /**
     * Method to get the application session object.
     *
     * @return  SessionInterface  The session object
     *
     * @since   2.0.0
     */
    public function getSession()
    {
        if ($this->session === null) {
            throw new \RuntimeException(\sprintf('A %s object has not been set.', SessionInterface::class));
        }

        return $this->session;
    }

    /**
     * Sets the session for the application to use, if required.
     *
     * @param   SessionInterface  $session  A session object.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Checks for a form token in the request.
     *
     * @param   string  $method  The request method in which to look for the token key.
     *
     * @return  boolean
     *
     * @since   2.0.0
     */
    public function checkToken($method = 'post')
    {
        $token = $this->getFormToken();

        // Support a token sent via the X-CSRF-Token header, then fall back to a token in the request
        $requestToken = $this->getInput()->server->get(
            'HTTP_X_CSRF_TOKEN',
            $this->getInput()->$method->get($token, '', 'alnum'),
            'alnum'
        );

        if (!$requestToken) {
            return false;
        }

        return $this->getSession()->hasToken($token);
    }

    /**
     * Method to determine a hash for anti-spoofing variable names
     *
     * @param   boolean  $forceNew  If true, force a new token to be created
     *
     * @return  string  Hashed var name
     *
     * @since   2.0.0
     */
    public function getFormToken($forceNew = false)
    {
        return $this->getSession()->getToken($forceNew);
    }
}
