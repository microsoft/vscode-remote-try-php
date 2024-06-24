<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Integrations\MailPoet\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Integrations\MailPoet\Blocks\BlockTypes\PoweredByMailpoet;

class BlockTypesController {
  private $poweredByMailPoet;

  public function __construct(
    PoweredByMailpoet $poweredByMailPoet
  ) {
    $this->poweredByMailPoet = $poweredByMailPoet;
  }

  public function initialize(): void {
    $this->poweredByMailPoet->initialize();
  }
}
