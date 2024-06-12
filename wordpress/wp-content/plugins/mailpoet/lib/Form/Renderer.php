<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\FormEntity;
use MailPoet\Form\Templates\FormTemplate;
use MailPoet\Form\Util\CustomFonts;
use MailPoet\Form\Util\Styles;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscription\Captcha\CaptchaConstants;

class Renderer {
  /** @var Styles */
  private $styleUtils;

  /** @var SettingsController */
  private $settings;

  /** @var BlocksRenderer */
  private $blocksRenderer;

  /** @var CustomFonts */
  private $customFonts;

  public function __construct(
    Styles $styleUtils,
    SettingsController $settings,
    CustomFonts $customFonts,
    BlocksRenderer $blocksRenderer
  ) {
    $this->styleUtils = $styleUtils;
    $this->settings = $settings;
    $this->blocksRenderer = $blocksRenderer;
    $this->customFonts = $customFonts;
  }

  public function renderStyles(FormEntity $form, string $prefix, string $displayType): string {
    $this->customFonts->enqueueStyle();
    $html = $this->styleUtils->prefixStyles($this->getCustomStyles($form), $prefix);
    $html .= strip_tags($this->styleUtils->renderFormSettingsStyles($form, $prefix, $displayType));
    return $html;
  }

  public function renderHTML(FormEntity $form = null): string {
    if (($form instanceof FormEntity) && !empty($form->getBody()) && is_array($form->getSettings())) {
      return $this->renderBlocks($form->getBody(), $form->getSettings() ?? [], $form->getId());
    }
    return '';
  }

  public function getCustomStyles(FormEntity $form = null): string {
    if (($form instanceof FormEntity) && (strlen(trim($form->getStyles() ?? '')) > 0)) {
      return strip_tags($form->getStyles() ?? '');
    } else {
      return FormTemplate::DEFAULT_STYLES;
    }
  }

  public function renderBlocks(
    array $blocks = [],
    array $formSettings = [],
    ?int $formId = null,
    bool $honeypotEnabled = true,
    bool $captchaEnabled = true
  ): string {
    // add honeypot for spambots
    $html = ($honeypotEnabled) ? $this->renderHoneypot() : '';
    foreach ($blocks as $key => $block) {
      if (
        $captchaEnabled
        && $block['type'] === FormEntity::SUBMIT_BLOCK_TYPE
        && CaptchaConstants::isRecaptcha($this->settings->get('captcha.type'))
      ) {
        $html .= $this->renderReCaptcha();
      }
      if (in_array($block['type'], [FormEntity::COLUMN_BLOCK_TYPE, FormEntity::COLUMNS_BLOCK_TYPE])) {
        $blocks = $block['body'] ?? [];
        $html .= $this->blocksRenderer->renderContainerBlock($block, $this->renderBlocks($blocks, $formSettings, $formId, false)) . PHP_EOL;
      } else {
        $html .= $this->blocksRenderer->renderBlock($block, $formSettings, $formId) . PHP_EOL;
      }
    }
    return $html;
  }

  private function renderHoneypot(): string {
    return '<label class="mailpoet_hp_email_label" style="display: none !important;">' . __('Please leave this field empty', 'mailpoet') . '<input type="email" name="data[email]"/></label>';
  }

  private function renderReCaptcha(): string {
    if ($this->settings->get('captcha.type') === CaptchaConstants::TYPE_RECAPTCHA) {
      $siteKey = $this->settings->get('captcha.recaptcha_site_token');
      $size = '';
    } else {
      $siteKey = $this->settings->get('captcha.recaptcha_invisible_site_token');
      $size = 'invisible';
    }

    $html = '<div class="mailpoet_recaptcha" data-sitekey="' . $siteKey . '" ' . ($size === 'invisible' ? 'data-size="invisible"' : '') . '>
      <div class="mailpoet_recaptcha_container"></div>
      <noscript>
        <div>
          <div class="mailpoet_recaptcha_noscript_container">
            <div>
              <iframe src="https://www.google.com/recaptcha/api/fallback?k=' . $siteKey . '" frameborder="0" scrolling="no">
              </iframe>
            </div>
          </div>
          <div class="mailpoet_recaptcha_noscript_input">
            <textarea id="g-recaptcha-response" name="data[recaptcha]" class="g-recaptcha-response">
            </textarea>
          </div>
        </div>
      </noscript>
      <input class="mailpoet_recaptcha_field" type="hidden" name="recaptchaWidgetId">
    </div>';

    return $html;
  }
}
