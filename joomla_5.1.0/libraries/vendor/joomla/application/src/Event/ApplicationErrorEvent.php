<?php

/**
 * Part of the Joomla Framework Application Package
 *
 * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Event;

use Joomla\Application\AbstractApplication;
use Joomla\Application\ApplicationEvents;

/**
 * Event class thrown when an application error occurs.
 *
 * @since  2.0.0
 */
class ApplicationErrorEvent extends ApplicationEvent
{
    /**
     * The Throwable object with the error data.
     *
     * @var    \Throwable
     * @since  2.0.0
     */
    private $error;

    /**
     * Event constructor.
     *
     * @param   \Throwable           $error        The Throwable object with the error data.
     * @param   AbstractApplication  $application  The active application.
     *
     * @since   2.0.0
     */
    public function __construct(\Throwable $error, AbstractApplication $application)
    {
        parent::__construct(ApplicationEvents::ERROR, $application);

        $this->error = $error;
    }

    /**
     * Get the error object.
     *
     * @return  \Throwable
     *
     * @since   2.0.0
     */
    public function getError(): \Throwable
    {
        return $this->error;
    }

    /**
     * Set the error object.
     *
     * @param   \Throwable  $error  The error object to set to the event.
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function setError(\Throwable $error): void
    {
        $this->error = $error;
    }
}
