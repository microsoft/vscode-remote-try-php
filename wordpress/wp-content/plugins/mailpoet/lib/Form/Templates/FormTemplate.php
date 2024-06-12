<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\FormEntity;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\CdnAssetUrl;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;

abstract class FormTemplate {
  const DEFAULT_STYLES = <<<EOL
/* form */
.mailpoet_form {
}

/* columns */
.mailpoet_column_with_background {
  padding: 10px;
}
/* space between columns */
.mailpoet_form_column:not(:first-child) {
  margin-left: 20px;
}

/* input wrapper (label + input) */
.mailpoet_paragraph {
  line-height:20px;
  margin-bottom: 20px;
}

/* labels */
.mailpoet_segment_label,
.mailpoet_text_label,
.mailpoet_textarea_label,
.mailpoet_select_label,
.mailpoet_radio_label,
.mailpoet_checkbox_label,
.mailpoet_list_label,
.mailpoet_date_label {
  display:block;
  font-weight: normal;
}

/* inputs */
.mailpoet_text,
.mailpoet_textarea,
.mailpoet_select,
.mailpoet_date_month,
.mailpoet_date_day,
.mailpoet_date_year,
.mailpoet_date {
  display:block;
}

.mailpoet_text,
.mailpoet_textarea {
  width: 200px;
}

.mailpoet_checkbox {
}

.mailpoet_submit {
}

.mailpoet_divider {
}

.mailpoet_message {
}

.mailpoet_form_loading {
  width: 30px;
  text-align: center;
  line-height: normal;
}

.mailpoet_form_loading > span {
  width: 5px;
  height: 5px;
  background-color: #5b5b5b;
}
EOL;

  /** @var CdnAssetUrl */
  protected $cdnAssetUrl;

  /** @var WPFunctions */
  protected $wp;

  /** @var string */
  protected $assetsDirectory = '';

  /** @var SettingsController */
  private $settings;

  public function __construct(
    CdnAssetUrl $cdnAssetUrl,
    SettingsController $settings,
    WPFunctions $wp
  ) {
    $this->cdnAssetUrl = $cdnAssetUrl;
    $this->wp = $wp;
    $this->settings = $settings;
  }

  abstract public function getName(): string;

  abstract public function getBody(): array;

  abstract public function getThumbnailUrl(): string;

  public function getSettings(): array {
    return [
      'on_success' => 'message',
      'success_message' => '',
      'segments' => null,
      'segments_selected_by' => 'admin',
    ];
  }

  public function getStyles(): string {
    return self::DEFAULT_STYLES;
  }

  public function toFormEntity(): FormEntity {
    $formEntity = new FormEntity($this->getName());
    $formEntity->setBody($this->getBody());
    $formEntity->setSettings($this->getSettings());
    $formEntity->setStyles($this->getStyles());
    $settings = $formEntity->getSettings();
    if (!isset($settings['success_message']) || !($settings['success_message'])) {
      $settings['success_message'] = $this->getDefaultSuccessMessage();
      $formEntity->setSettings($settings);
    }
    return $formEntity;
  }

  private function getDefaultSuccessMessage() {
    if ($this->settings->get('signup_confirmation.enabled')) {
      return __('Check your inbox or spam folder to confirm your subscription.', 'mailpoet');
    }
    return __('You’ve been successfully subscribed to our newsletter!', 'mailpoet');
  }

  protected function getAssetUrl(string $filename): string {
    return $this->cdnAssetUrl->generateCdnUrl("form-templates/{$this->assetsDirectory}/$filename");
  }

  protected function replaceLinkTags($source, $link, $attributes = [], $linkTag = false): string {
    return Helpers::replaceLinkTags($source, $link, $attributes, $linkTag);
  }

  protected function replacePrivacyLinkTags($source, $link = '#'): string {
    $privacyPolicyUrl = $this->wp->getPrivacyPolicyUrl();
    $attributes = [];
    $linkTag = false;

    if (!empty($privacyPolicyUrl)) {
      $link = $this->wp->escUrl($privacyPolicyUrl);
      $attributes = ['target' => '_blank'];
      $linkTag = 'link';
    }
    return $this->replaceLinkTags($source, $link, $attributes, $linkTag);
  }
}
