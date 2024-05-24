<?php

/**
 * Part of the Joomla Framework Application Package
 *
 * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
 * @license        GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application;

use Joomla\Application\Controller\ControllerResolverInterface;
use Joomla\Application\Web\WebClient;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Joomla\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A basic web application class for handing HTTP requests.
 *
 * @since  2.0.0
 */
class WebApplication extends AbstractWebApplication implements SessionAwareWebApplicationInterface
{
    use SessionAwareWebApplicationTrait;

    /**
     * The application's controller resolver.
     *
     * @var    ControllerResolverInterface
     * @since  2.0.0
     */
    protected $controllerResolver;

    /**
     * The application's router.
     *
     * @var    RouterInterface
     * @since  2.0.0
     */
    protected $router;

    /**
     * Class constructor.
     *
     * @param  ControllerResolverInterface  $controllerResolver   The application's controller resolver
     * @param  RouterInterface              $router               The application's router
     * @param  Input                        ?$input               An optional argument to provide dependency injection
     *                                                            for the application's input object.  If the argument
     *                                                            is an Input object that object will become the
     *                                                            application's input object, otherwise a default input
     *                                                            object is created.
     * @param  Registry                     ?$config              An optional argument to provide dependency injection
     *                                                            for the application's config object.  If the argument
     *                                                            is a Registry object that object will become the
     *                                                            application's config object, otherwise a default
     *                                                            config object is created.
     * @param  Web\WebClient                ?$client              An optional argument to provide dependency injection
     *                                                            for the application's client object.  If the argument
     *                                                            is a Web\WebClient object that object will become the
     *                                                            application's client object, otherwise a default
     *                                                            client object is created.
     * @param  ResponseInterface            ?$response            An optional argument to provide dependency injection
     *                                                            for the application's response object.  If the
     *                                                            argument is a ResponseInterface object that object
     *                                                            will become the application's response object,
     *                                                            otherwise a default response object is created.
     *
     * @since   2.0.0
     */
    public function __construct(
        ControllerResolverInterface $controllerResolver,
        RouterInterface $router,
        Input $input = null,
        Registry $config = null,
        WebClient $client = null,
        ResponseInterface $response = null
    ) {
        $this->controllerResolver = $controllerResolver;
        $this->router             = $router;

        // Call the constructor as late as possible (it runs `initialise`).
        parent::__construct($input, $config, $client, $response);
    }

    /**
     * Method to run the application routines.
     *
     * @return  void
     *
     * @since   2.0.0
     */
    protected function doExecute(): void
    {
        $route = $this->router->parseRoute($this->get('uri.route'), $this->input->getMethod());

        // Add variables to the input if not already set
        foreach ($route->getRouteVariables() as $key => $value) {
            $this->input->def($key, $value);
        }

        \call_user_func($this->controllerResolver->resolve($route));
    }
}
