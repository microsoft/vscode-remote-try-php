<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\WooCommerce\TransactionalEmails;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Editor\LayoutHelper;
use MailPoet\WooCommerce\TransactionalEmails;

class ContentPreprocessor {
  public const WC_HEADING_PLACEHOLDER = '[mailpoet_woocommerce_heading_placeholder]';
  public const WC_CONTENT_PLACEHOLDER = '[mailpoet_woocommerce_content_placeholder]';

  public const WC_HEADING_BEFORE = '
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
            <tr>
              <td class="mailpoet_text" valign="top" style="padding-top:20px;padding-bottom:20px;word-break:break-word;word-wrap:break-word;">';
  public const WC_HEADING_AFTER = '
        </td>
      </tr>
    </table>';

  /** @var TransactionalEmails */
  private $transactionalEmails;

  public function __construct(
    TransactionalEmails $transactionalEmails
  ) {
    $this->transactionalEmails = $transactionalEmails;
  }

  public function preprocessContent() {
    return $this->renderPlaceholderBlock(self::WC_CONTENT_PLACEHOLDER);
  }

  public function preprocessHeader() {
    $wcEmailSettings = $this->transactionalEmails->getWCEmailSettings();
    $content = self::WC_HEADING_BEFORE . '<h1 style="color:' . $wcEmailSettings['base_text_color'] . ';">' . self::WC_HEADING_PLACEHOLDER . '</h1>' . self::WC_HEADING_AFTER;
    return $this->renderTextBlock($content, ['backgroundColor' => $wcEmailSettings['base_color']]);
  }

  private function renderTextBlock(string $text, array $styles = []): array {
    return [
      LayoutHelper::row([
        LayoutHelper::col([[
          'type' => 'text',
          'text' => $text,
        ]]),
      ], $styles),
    ];
  }

  private function renderPlaceholderBlock(string $placeholder): array {
    return [
      LayoutHelper::row([
        LayoutHelper::col([[
          'type' => 'placeholder',
          'placeholder' => $placeholder,
        ]]),
      ]),
    ];
  }
}
