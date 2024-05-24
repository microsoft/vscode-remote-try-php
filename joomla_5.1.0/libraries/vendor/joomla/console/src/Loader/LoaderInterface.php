<?php

/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Loader;

use Joomla\Console\Command\AbstractCommand;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Interface defining a command loader.
 *
 * @since  2.0.0
 */
interface LoaderInterface
{
    /**
     * Loads a command.
     *
     * @param   string  $name  The command to load.
     *
     * @return  AbstractCommand
     *
     * @since   2.0.0
     * @throws  CommandNotFoundException
     */
    public function get(string $name): AbstractCommand;

    /**
     * Get the names of the registered commands.
     *
     * @return  string[]
     *
     * @since   2.0.0
     */
    public function getNames(): array;

    /**
     * Checks if a command exists.
     *
     * @param   string  $name  The command to check.
     *
     * @return  boolean
     *
     * @since   2.0.0
     */
    public function has($name): bool;
}
