<?php declare(strict_types = 1);

namespace MailPoet\Newsletter\Renderer\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class Placeholder {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function render($element): string {
    $placeholder = $element['placeholder'];
    $class = $element['class'] ?? '';
    $style = $element['style'] ?? '';
    return '
      <tr>
        <td class="' . $this->wp->escAttr($class) . '" style="' . $this->wp->escAttr($style) . '">
          ' . $this->wp->escHtml($placeholder) . '
        </td>
      </tr>';
  }
}
