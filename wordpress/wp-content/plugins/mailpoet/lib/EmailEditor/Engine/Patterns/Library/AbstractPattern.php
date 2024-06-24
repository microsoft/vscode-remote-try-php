<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Patterns\Library;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\CdnAssetUrl;

abstract class AbstractPattern {
  protected $cdnAssetUrl;
  protected $blockTypes = [];
  protected $inserter = true;
  protected $source = 'plugin';
  protected $categories = ['mailpoet'];
  protected $viewportWidth = 620;

  public function __construct(
    CdnAssetUrl $cdnAssetUrl
  ) {
    $this->cdnAssetUrl = $cdnAssetUrl;
  }

  public function getProperties() {
    return [
      'title' => $this->getTitle(),
      'content' => $this->getContent(),
      'description' => $this->getDescription(),
      'categories' => $this->categories,
      'inserter' => $this->inserter,
      'blockTypes' => $this->blockTypes,
      'source' => $this->source,
      'viewportWidth' => $this->viewportWidth,
    ];
  }

  abstract protected function getContent(): string;

  abstract protected function getTitle(): string;

  protected function getDescription(): string {
    return '';
  }
}
