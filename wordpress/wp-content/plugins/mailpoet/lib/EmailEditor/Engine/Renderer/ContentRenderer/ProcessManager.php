<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Renderer\ContentRenderer;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Postprocessors\HighlightingPostprocessor;
use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Postprocessors\Postprocessor;
use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Postprocessors\VariablesPostprocessor;
use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Preprocessors\BlocksWidthPreprocessor;
use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Preprocessors\CleanupPreprocessor;
use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Preprocessors\Preprocessor;
use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Preprocessors\SpacingPreprocessor;
use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Preprocessors\TypographyPreprocessor;

class ProcessManager {
  /** @var Preprocessor[] */
  private $preprocessors = [];

  /** @var Postprocessor[] */
  private $postprocessors = [];

  public function __construct(
    CleanupPreprocessor $cleanupPreprocessor,
    BlocksWidthPreprocessor $blocksWidthPreprocessor,
    TypographyPreprocessor $typographyPreprocessor,
    SpacingPreprocessor $spacingPreprocessor,
    HighlightingPostprocessor $highlightingPostprocessor,
    VariablesPostprocessor $variablesPostprocessor
  ) {
    $this->registerPreprocessor($cleanupPreprocessor);
    $this->registerPreprocessor($blocksWidthPreprocessor);
    $this->registerPreprocessor($typographyPreprocessor);
    $this->registerPreprocessor($spacingPreprocessor);
    $this->registerPostprocessor($highlightingPostprocessor);
    $this->registerPostprocessor($variablesPostprocessor);
  }

  /**
   * @param array $parsedBlocks
   * @param array{contentSize: string} $layout
   * @param array{spacing: array{padding: array{bottom: string, left: string, right: string, top: string}, blockGap: string}} $styles
   * @return array
   */
  public function preprocess(array $parsedBlocks, array $layout, array $styles): array {
    foreach ($this->preprocessors as $preprocessor) {
      $parsedBlocks = $preprocessor->preprocess($parsedBlocks, $layout, $styles);
    }
    return $parsedBlocks;
  }

  public function postprocess(string $html): string {
    foreach ($this->postprocessors as $postprocessor) {
      $html = $postprocessor->postprocess($html);
    }
    return $html;
  }

  public function registerPreprocessor(Preprocessor $preprocessor): void {
    $this->preprocessors[] = $preprocessor;
  }

  public function registerPostprocessor(Postprocessor $postprocessor): void {
    $this->postprocessors[] = $postprocessor;
  }
}
