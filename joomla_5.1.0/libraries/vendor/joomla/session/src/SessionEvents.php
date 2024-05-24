<?php

/**
 * Part of the Joomla Framework Session Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session;

/**
 * Class defining the events dispatched by the session API
 *
 * @since  2.0.0
 */
final class SessionEvents
{
    /**
     * Private constructor to prevent instantiation of this class
     *
     * @since   2.0.0
     */
    private function __construct()
    {
    }

    /**
     * Session event which is dispatched after the session has been started.
     *
     * Listeners to this event receive a `Joomla\Session\SessionEvent` object.
     *
     * @var    string
     * @since  2.0.0
     */
    public const START = 'session.start';

    /**
     * Session event which is dispatched after the session has been restarted.
     *
     * Listeners to this event receive a `Joomla\Session\SessionEvent` object.
     *
     * @var    string
     * @since  2.0.0
     */
    public const RESTART = 'session.restart';
}
