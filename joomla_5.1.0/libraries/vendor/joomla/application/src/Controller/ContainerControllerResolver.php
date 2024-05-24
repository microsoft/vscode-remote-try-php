<?php

/**
 * Part of the Joomla Framework Application Package
 *
 * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Controller;

use Psr\Container\ContainerInterface;

/**
 * Controller resolver which supports creating controllers from a PSR-11 compatible container
 *
 * Controllers must be registered in the container using their FQCN as a service key
 *
 * @since  2.0.0
 */
class ContainerControllerResolver extends ControllerResolver
{
    /**
     * The container to search for controllers in
     *
     * @var    ContainerInterface
     * @since  2.0.0
     */
    private $container;

    /**
     * Constructor
     *
     * @param   ContainerInterface  $container  The container to search for controllers in
     *
     * @since   2.0.0
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Instantiate a controller class
     *
     * @param   string  $class  The class to instantiate
     *
     * @return  object  Controller class instance
     *
     * @since   2.0.0
     */
    protected function instantiateController(string $class): object
    {
        if ($this->container->has($class)) {
            return $this->container->get($class);
        }

        return parent::instantiateController($class);
    }
}
