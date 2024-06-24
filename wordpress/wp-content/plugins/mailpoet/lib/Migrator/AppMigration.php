<?php declare(strict_types = 1);

namespace MailPoet\Migrator;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoetVendor\Doctrine\ORM\EntityManager;

abstract class AppMigration {
  /** @var ContainerWrapper */
  protected $container;

  /** @var EntityManager */
  protected $entityManager;

  public function __construct(
    ContainerWrapper $container
  ) {
    $this->container = $container;
    $this->entityManager = $container->get(EntityManager::class);
  }

  abstract public function run(): void;
}
