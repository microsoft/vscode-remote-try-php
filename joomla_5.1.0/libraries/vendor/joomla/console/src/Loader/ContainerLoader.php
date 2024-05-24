<?php

/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Loader;

use Joomla\Console\Command\AbstractCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * PSR-11 compatible command loader.
 *
 * @since  2.0.0
 */
final class ContainerLoader implements LoaderInterface
{
    /**
     * The service container.
     *
     * @var    ContainerInterface
     * @since  2.0.0
     */
    private $container;

    /**
     * The command name to service ID map.
     *
     * @var    string[]
     * @since  2.0.0
     */
    private $commandMap;

    /**
     * Constructor.
     *
     * @param   ContainerInterface  $container   A container from which to load command services.
     * @param   array               $commandMap  An array with command names as keys and service IDs as values.
     *
     * @since   2.0.0
     */
    public function __construct(ContainerInterface $container, array $commandMap)
    {
        $this->container  = $container;
        $this->commandMap = $commandMap;
    }

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
    public function get(string $name): AbstractCommand
    {
        if (!$this->has($name)) {
            throw new CommandNotFoundException(sprintf('Command "%s" does not exist.', $name));
        }

        return $this->container->get($this->commandMap[$name]);
    }

    /**
     * Get the names of the registered commands.
     *
     * @return  string[]
     *
     * @since   2.0.0
     */
    public function getNames(): array
    {
        return array_keys($this->commandMap);
    }

    /**
     * Checks if a command exists.
     *
     * @param   string  $name  The command to check.
     *
     * @return  boolean
     *
     * @since   2.0.0
     */
    public function has($name): bool
    {
        return isset($this->commandMap[$name]) && $this->container->has($this->commandMap[$name]);
    }
}
