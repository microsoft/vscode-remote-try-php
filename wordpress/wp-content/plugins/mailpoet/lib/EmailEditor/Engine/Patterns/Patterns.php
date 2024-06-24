<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Patterns;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\CdnAssetUrl;

class Patterns {
  private $namespace = 'mailpoet';
  protected $cdnAssetUrl;

  public function __construct(
    CdnAssetUrl $cdnAssetUrl
  ) {
    $this->cdnAssetUrl = $cdnAssetUrl;
  }

  public function initialize(): void {
    $this->registerBlockPatternCategory();
    $this->registerPatterns();
  }

  private function registerBlockPatternCategory() {
    register_block_pattern_category(
      'mailpoet',
      [
        'label' => _x('MailPoet', 'Block pattern category', 'mailpoet'),
        'description' => __('A collection of email template layouts.', 'mailpoet'),
      ]
    );
  }

  private function registerPatterns() {
    $this->registerPattern('default', new Library\DefaultContent($this->cdnAssetUrl));
  }

  private function registerPattern($name, $pattern) {
    register_block_pattern($this->namespace . '/' . $name, $pattern->getProperties());
  }
}
