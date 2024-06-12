<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\PostEditorBlocks;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoet\Config\Renderer;
use MailPoet\WP\Functions as WPFunctions;

class PostEditorBlock {
  /** @var Renderer */
  private $renderer;

  /** @var WPFunctions */
  private $wp;

  /** @var SubscriptionFormBlock */
  private $subscriptionFormBlock;

  public function __construct(
    Renderer $renderer,
    WPFunctions $wp,
    SubscriptionFormBlock $subscriptionFormBlock
  ) {
    $this->renderer = $renderer;
    $this->wp = $wp;
    $this->subscriptionFormBlock = $subscriptionFormBlock;
  }

  public function init() {
    $this->subscriptionFormBlock->init();

    if (is_admin()) {
      $this->initAdmin();
    } else {
      $this->initFrontend();
    }
  }

  private function initAdmin() {
    $this->wp->addAction('enqueue_block_editor_assets', [$this, 'enqueueAssets']);
    $this->subscriptionFormBlock->initAdmin();
  }

  public function enqueueAssets() {
    $this->wp->wpEnqueueScript(
      'mailpoet-block-form-block-js',
      Env::$assetsUrl . '/dist/js/' . $this->renderer->getJsAsset('post_editor_block.js'),
      ['wp-blocks', 'wp-components', 'wp-server-side-render', 'wp-block-editor'],
      Env::$version,
      true
    );

    $this->wp->wpEnqueueStyle(
      'mailpoetblock-form-block-css',
      Env::$assetsUrl . '/dist/css/' . $this->renderer->getCssAsset('mailpoet-post-editor-block.css'),
      ['wp-edit-blocks'],
      Env::$version
    );
  }

  private function initFrontend() {
    $this->subscriptionFormBlock->initFrontend();
  }
}
