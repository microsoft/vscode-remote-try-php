<?php

/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Event;

use Joomla\Console\Application;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Console\ConsoleEvents;

/**
 * Event triggered immediately before the process is terminated.
 *
 * @since  2.0.0
 */
class TerminateEvent extends ConsoleEvent
{
    /**
     * The exit code to use for the application.
     *
     * @var    integer
     * @since  2.0.0
     */
    private $exitCode;

    /**
     * Event constructor.
     *
     * @param   integer               $exitCode     The Throwable object with the error data.
     * @param   Application           $application  The active application.
     * @param   AbstractCommand|null  $command      The command being executed.
     *
     * @since   2.0.0
     */
    public function __construct(int $exitCode, Application $application, ?AbstractCommand $command = null)
    {
        parent::__construct(ConsoleEvents::TERMINATE, $application, $command);

        $this->exitCode = $exitCode;
    }

    /**
     * Gets the exit code.
     *
     * @return  integer
     *
     * @since   2.0.0
     */
    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * Sets the exit code.
     *
     * @param   integer  $exitCode  The command exit code.
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function setExitCode(int $exitCode): void
    {
        $this->exitCode = $exitCode;
    }
}
