<?php

/**
 * Part of the Joomla Framework Application Package
 *
 * @copyright  (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Event;

use Joomla\Application\AbstractApplication;
use Joomla\Event\Event;

/**
 * Base event class for application events.
 *
 * @since  2.0.0
 */
class ApplicationEvent extends Event
{
    /**
     * The active application.
     *
     * @var    AbstractApplication
     * @since  2.0.0
     */
    private $application;

    /**
     * Event constructor.
     *
     * @param   string               $name         The event name.
     * @param   AbstractApplication  $application  The active application.
     *
     * @since   2.0.0
     */
    public function __construct(string $name, AbstractApplication $application)
    {
        parent::__construct($name);

        $this->application = $application;
    }

    /**
     * Get the active application.
     *
     * @return  AbstractApplication
     *
     * @since   2.0.0
     */
    public function getApplication(): AbstractApplication
    {
        return $this->application;
    }
}
