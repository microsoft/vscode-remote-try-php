<?php

/**
 * Part of the Joomla Framework Session Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session;

/**
 * Class defining the various states of a session
 *
 * @since  2.0.0
 */
final class SessionState
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
     * State indicating the session is active.
     *
     * A `SessionInterface` instance should be in this state once the session has started.
     *
     * @var    string
     * @since  2.0.0
     */
    public const ACTIVE = 'active';

    /**
     * State indicating the session is closed.
     *
     * A `SessionInterface` instance should be in this state after calling the `close()` method.
     *
     * @var    string
     * @since  2.0.0
     */
    public const CLOSED = 'closed';

    /**
     * State indicating the session is destroyed.
     *
     * A `SessionInterface` instance should be in this state after calling the `destroy()` method.
     *
     * @var    string
     * @since  2.0.0
     */
    public const DESTROYED = 'destroyed';

    /**
     * State indicating the session is in an error state.
     *
     * A `SessionInterface` instance should be in this state if the session cannot be validated after being started.
     *
     * @var    string
     * @since  2.0.0
     */
    public const ERROR = 'error';

    /**
     * State indicating the session is expired.
     *
     * A `SessionInterface` instance should be in this state if the session has passed the allowed lifetime.
     * A `SessionInterface` instance may be in this state if validating a session token fails.
     *
     * @var    string
     * @since  2.0.0
     */
    public const EXPIRED = 'expired';

    /**
     * State indicating the session is inactive.
     *
     * A `SessionInterface` instance should begin in this state.
     *
     * @var    string
     * @since  2.0.0
     */
    public const INACTIVE = 'inactive';
}
