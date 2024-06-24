<?php declare(strict_types = 1);

namespace MailPoet\API\REST;

if (!defined('ABSPATH')) exit;


use MailPoet\InvalidStateException;
use MailPoetVendor\Psr\Container\ContainerInterface;

class EndpointContainer {
  /** @var ContainerInterface */
  private $container;

  public function __construct(
    ContainerInterface $container
  ) {
    $this->container = $container;
  }

  public function get(string $class): Endpoint {
    $endpoint = $this->container->get($class);
    if (!$endpoint instanceof Endpoint) {
      throw new InvalidStateException(sprintf("Class '%s' doesn't implement '%s'", $class, Endpoint::class));
    }
    return $endpoint;
  }
}
