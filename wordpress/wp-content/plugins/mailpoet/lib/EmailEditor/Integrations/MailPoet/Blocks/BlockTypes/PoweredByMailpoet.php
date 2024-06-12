<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Integrations\MailPoet\Blocks\BlockTypes;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\ServicesChecker;
use MailPoet\Util\CdnAssetUrl;

class PoweredByMailpoet extends AbstractBlock {
  private ServicesChecker $servicesChecker;
  private CdnAssetUrl $cdnAssetUrl;
  protected $blockName = 'powered-by-mailpoet';

  public function __construct(
    ServicesChecker $servicesChecker,
    CdnAssetUrl $cdnAssetUrl
  ) {
    $this->cdnAssetUrl = $cdnAssetUrl;
    $this->servicesChecker = $servicesChecker;
  }

  public function render($attributes, $content, $block) {
    if ($this->servicesChecker->isPremiumPluginActive()) {
      return '';
    }

    $logo = $attributes['logo'] ?? 'default';
    $logoUrl = $this->cdnAssetUrl->generateCdnUrl('email-editor/logo-' . $logo . '.png');

    return $this->addSpacer(sprintf(
      '<div class="%1$s" style="text-align:center">%2$s</div>',
      esc_attr('wp-block-' . $this->blockName),
      '<img src="' . esc_attr($logoUrl) . '" alt="Powered by MailPoet" width="100px" />'
    ), $block->parsed_block['email_attrs'] ?? []); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
  }
}
