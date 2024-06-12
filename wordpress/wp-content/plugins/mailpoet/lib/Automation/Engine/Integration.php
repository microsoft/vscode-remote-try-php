<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine;

if (!defined('ABSPATH')) exit;


interface Integration {
  public function register(Registry $registry): void;
}
