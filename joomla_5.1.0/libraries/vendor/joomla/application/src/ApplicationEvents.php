<?php

/**
 * Part of the Joomla Framework Application Package
 *
 * @copyright  (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application;

/**
 * Class defining the events available in the application.
 *
 * @since  2.0.0
 */
final class ApplicationEvents
{
    /**
     * The ERROR event is an event triggered when a Throwable is uncaught.
     *
     * This event allows you to inspect the Throwable and implement additional error handling/reporting mechanisms.
     *
     * @var    string
     * @since  2.0.0
     */
    public const ERROR = 'application.error';

    /**
     * The BEFORE_EXECUTE event is an event triggered before the application is executed.
     *
     * @var    string
     * @since  2.0.0
     */
    public const BEFORE_EXECUTE = 'application.before_execute';

    /**
     * The AFTER_EXECUTE event is an event triggered after the application is executed.
     *
     * @var    string
     * @since  2.0.0
     */
    public const AFTER_EXECUTE = 'application.after_execute';

    /**
     * The BEFORE_RESPOND event is an event triggered before the application response is sent.
     *
     * @var    string
     * @since  2.0.0
     */
    public const BEFORE_RESPOND = 'application.before_respond';

    /**
     * The AFTER_RESPOND event is an event triggered after the application response is sent.
     *
     * @var    string
     * @since  2.0.0
     */
    public const AFTER_RESPOND = 'application.after_respond';
}
