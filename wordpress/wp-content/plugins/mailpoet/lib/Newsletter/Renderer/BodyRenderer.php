<?php declare(strict_types = 1);

namespace MailPoet\Newsletter\Renderer;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;

class BodyRenderer {
  /** @var Blocks\Renderer */
  private $blocksRenderer;

  /** @var Columns\Renderer */
  private $columnsRenderer;

  public function __construct(
    Blocks\Renderer $blocksRenderer,
    Columns\Renderer $columnsRenderer
  ) {
    $this->blocksRenderer = $blocksRenderer;
    $this->columnsRenderer = $columnsRenderer;
  }

  /**
   * @param NewsletterEntity $newsletter
   * @param array $content
   * @return string
   */
  public function renderBody(NewsletterEntity $newsletter, array $content) {
    $blocks = (array_key_exists('blocks', $content))
      ? $content['blocks']
      : [];

    $renderedContent = [];
    foreach ($blocks as $contentBlock) {
      $columnsData = $this->blocksRenderer->render($newsletter, $contentBlock);

      $renderedContent[] = $this->columnsRenderer->render(
        $contentBlock,
        $columnsData
      );
    }
    return implode('', $renderedContent);
  }
}
