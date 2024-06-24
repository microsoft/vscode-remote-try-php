<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\DI;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Symfony\Component\DependencyInjection\Container;
use MailPoetVendor\Symfony\Component\DependencyInjection\ContainerBuilder;

class ContainerFactory {

  /** @var IContainerConfigurator */
  private $configurator;

  public function __construct(
    IContainerConfigurator $configurator
  ) {
    $this->configurator = $configurator;
  }

  /**
   * @return Container
   */
  public function getContainer() {
    $dumpClass = '\\' . $this->configurator->getDumpNamespace() . '\\' . $this->configurator->getDumpClassname();
    if (class_exists($dumpClass)) {
      $container = new $dumpClass();
    } else { // Only for dev environment
      $container = $this->getConfiguredContainer();
      $container->compile();
    }
    return $container;
  }

  public function getConfiguredContainer() {
    return $this->configurator->configure(new ContainerBuilder());
  }
}
