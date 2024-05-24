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
 * Event triggered when an uncaught Throwable is received by the application from a command.
 *
 * @since  2.0.0
 */
class CommandErrorEvent extends ConsoleEvent
{
    /**
     * The Throwable object with the error data.
     *
     * @var    \Throwable
     * @since  2.0.0
     */
    private $error;

    /**
     * The exit code to use for the application.
     *
     * @var    integer|null
     * @since  2.0.0
     */
    private $exitCode;

    /**
     * Event constructor.
     *
     * @param   \Throwable            $error        The Throwable object with the error data.
     * @param   Application           $application  The active application.
     * @param   AbstractCommand|null  $command      The command being executed.
     *
     * @since   2.0.0
     */
    public function __construct(\Throwable $error, Application $application, ?AbstractCommand $command = null)
    {
        parent::__construct(ConsoleEvents::COMMAND_ERROR, $application, $command);

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
     * Gets the exit code.
     *
     * @return  integer
     *
     * @since   2.0.0
     */
    public function getExitCode(): int
    {
        if ($this->exitCode !== null) {
            return $this->exitCode;
        }

        return \is_int($this->error->getCode()) && $this->error->getCode() !== 0 ? $this->error->getCode() : 1;
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

        $r = new \ReflectionProperty($this->error, 'code');
        $r->setAccessible(true);
        $r->setValue($this->error, $this->exitCode);
    }
}
