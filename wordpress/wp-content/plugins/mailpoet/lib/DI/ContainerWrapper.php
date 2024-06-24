<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\DI;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Psr\Container\ContainerInterface;
use MailPoetVendor\Psr\Container\NotFoundExceptionInterface;
use MailPoetVendor\Symfony\Component\DependencyInjection\Container;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ContainerWrapper implements ContainerInterface {

  /** @var Container */
  private $freeContainer;

  /** @var Container|null */
  private $premiumContainer;

  /** @var ContainerWrapper|null */
  private static $instance;

  public function __construct(
    Container $freeContainer,
    Container $premiumContainer = null
  ) {
    $this->freeContainer = $freeContainer;
    $this->premiumContainer = $premiumContainer;
  }

  /**
   * @template T
   * @param class-string<T> $id
   * @return T
   */
  public function get($id) {
    try {
      $result = $this->freeContainer->get($id);
      if (!$result instanceof $id) {
        throw new ServiceNotFoundException('Service Not Found ' . $id);
      }
      return $result;
    } catch (NotFoundExceptionInterface $e) {
      if (!$this->premiumContainer || !$this->premiumContainer->has($id)) {
        throw $e;
      }
      $result = $this->premiumContainer->get($id);
      if (!$result instanceof $id) {
        throw new ServiceNotFoundException('Service Not Found ' . $id);
      }
      return $result;
    }
  }

  public function has($id) {
    return $this->freeContainer->has($id) || ($this->premiumContainer && $this->premiumContainer->has($id));
  }

  /**
   * @return ContainerInterface|null
   */
  public function getPremiumContainer() {
    if (!$this->premiumContainer && class_exists(\MailPoet\Premium\DI\ContainerConfigurator::class)) {
      $this->premiumContainer = self::createPremiumContainer($this->freeContainer);
    }
    return $this->premiumContainer;
  }

  public static function getInstance($debug = false): ContainerWrapper {
    if (self::$instance) {
      return self::$instance;
    }
    $freeContainerFactory = new ContainerFactory(new ContainerConfigurator());
    $freeContainer = $freeContainerFactory->getContainer();
    $premiumContainer = null;
    if (class_exists(\MailPoet\Premium\DI\ContainerConfigurator::class)) {
      $premiumContainer = self::createPremiumContainer($freeContainer);
    }
    self::$instance = new ContainerWrapper($freeContainer, $premiumContainer);
    return self::$instance;
  }

  private static function createPremiumContainer(Container $freeContainer) {
    $premiumContainerFactory = new ContainerFactory(new \MailPoet\Premium\DI\ContainerConfigurator());
    $premiumContainer = $premiumContainerFactory->getContainer();
    $premiumContainer->set(IContainerConfigurator::FREE_CONTAINER_SERVICE_SLUG, $freeContainer);
    $freeContainer->set(IContainerConfigurator::PREMIUM_CONTAINER_SERVICE_SLUG, $premiumContainer);
    return $premiumContainer;
  }
}
