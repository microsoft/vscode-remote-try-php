<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\Templates\Templates\InitialForm;
use MailPoet\Form\Templates\Templates\Template10BelowPages;
use MailPoet\Form\Templates\Templates\Template10FixedBar;
use MailPoet\Form\Templates\Templates\Template10Popup;
use MailPoet\Form\Templates\Templates\Template10SlideIn;
use MailPoet\Form\Templates\Templates\Template10Widget;
use MailPoet\Form\Templates\Templates\Template11BelowPages;
use MailPoet\Form\Templates\Templates\Template11FixedBar;
use MailPoet\Form\Templates\Templates\Template11Popup;
use MailPoet\Form\Templates\Templates\Template11SlideIn;
use MailPoet\Form\Templates\Templates\Template11Widget;
use MailPoet\Form\Templates\Templates\Template12BelowPages;
use MailPoet\Form\Templates\Templates\Template12FixedBar;
use MailPoet\Form\Templates\Templates\Template12Popup;
use MailPoet\Form\Templates\Templates\Template12SlideIn;
use MailPoet\Form\Templates\Templates\Template12Widget;
use MailPoet\Form\Templates\Templates\Template13BelowPages;
use MailPoet\Form\Templates\Templates\Template13FixedBar;
use MailPoet\Form\Templates\Templates\Template13Popup;
use MailPoet\Form\Templates\Templates\Template13SlideIn;
use MailPoet\Form\Templates\Templates\Template13Widget;
use MailPoet\Form\Templates\Templates\Template14BelowPages;
use MailPoet\Form\Templates\Templates\Template14FixedBar;
use MailPoet\Form\Templates\Templates\Template14Popup;
use MailPoet\Form\Templates\Templates\Template14SlideIn;
use MailPoet\Form\Templates\Templates\Template14Widget;
use MailPoet\Form\Templates\Templates\Template17BelowPages;
use MailPoet\Form\Templates\Templates\Template17FixedBar;
use MailPoet\Form\Templates\Templates\Template17Popup;
use MailPoet\Form\Templates\Templates\Template17SlideIn;
use MailPoet\Form\Templates\Templates\Template17Widget;
use MailPoet\Form\Templates\Templates\Template18BelowPages;
use MailPoet\Form\Templates\Templates\Template18FixedBar;
use MailPoet\Form\Templates\Templates\Template18Popup;
use MailPoet\Form\Templates\Templates\Template18SlideIn;
use MailPoet\Form\Templates\Templates\Template18Widget;
use MailPoet\Form\Templates\Templates\Template1BelowPages;
use MailPoet\Form\Templates\Templates\Template1FixedBar;
use MailPoet\Form\Templates\Templates\Template1Popup;
use MailPoet\Form\Templates\Templates\Template1SlideIn;
use MailPoet\Form\Templates\Templates\Template1Widget;
use MailPoet\Form\Templates\Templates\Template3BelowPages;
use MailPoet\Form\Templates\Templates\Template3FixedBar;
use MailPoet\Form\Templates\Templates\Template3Popup;
use MailPoet\Form\Templates\Templates\Template3SlideIn;
use MailPoet\Form\Templates\Templates\Template3Widget;
use MailPoet\Form\Templates\Templates\Template4BelowPages;
use MailPoet\Form\Templates\Templates\Template4FixedBar;
use MailPoet\Form\Templates\Templates\Template4Popup;
use MailPoet\Form\Templates\Templates\Template4SlideIn;
use MailPoet\Form\Templates\Templates\Template4Widget;
use MailPoet\Form\Templates\Templates\Template6BelowPages;
use MailPoet\Form\Templates\Templates\Template6FixedBar;
use MailPoet\Form\Templates\Templates\Template6Popup;
use MailPoet\Form\Templates\Templates\Template6SlideIn;
use MailPoet\Form\Templates\Templates\Template6Widget;
use MailPoet\Form\Templates\Templates\Template7BelowPages;
use MailPoet\Form\Templates\Templates\Template7FixedBar;
use MailPoet\Form\Templates\Templates\Template7Popup;
use MailPoet\Form\Templates\Templates\Template7SlideIn;
use MailPoet\Form\Templates\Templates\Template7Widget;
use MailPoet\Settings\SettingsController;
use MailPoet\UnexpectedValueException;
use MailPoet\Util\CdnAssetUrl;
use MailPoet\WP\Functions as WPFunctions;

class TemplateRepository {
  const INITIAL_FORM_TEMPLATE = InitialForm::ID;

  /** @var CdnAssetUrl */
  private $cdnAssetUrl;

  /** @var WPFunctions */
  private $wp;

  /** @var SettingsController */
  private $settings;


  private $templates = [
    InitialForm::ID => InitialForm::class,
    Template1BelowPages::ID => Template1BelowPages::class,
    Template1FixedBar::ID => Template1FixedBar::class,
    Template1Popup::ID => Template1Popup::class,
    Template1SlideIn::ID => Template1SlideIn::class,
    Template1Widget::ID => Template1Widget::class,
    Template3BelowPages::ID => Template3BelowPages::class,
    Template3FixedBar::ID => Template3FixedBar::class,
    Template3Popup::ID => Template3Popup::class,
    Template3SlideIn::ID => Template3SlideIn::class,
    Template3Widget::ID => Template3Widget::class,
    Template4BelowPages::ID => Template4BelowPages::class,
    Template4FixedBar::ID => Template4FixedBar::class,
    Template4Popup::ID => Template4Popup::class,
    Template4SlideIn::ID => Template4SlideIn::class,
    Template4Widget::ID => Template4Widget::class,
    Template6BelowPages::ID => Template6BelowPages::class,
    Template6FixedBar::ID => Template6FixedBar::class,
    Template6Popup::ID => Template6Popup::class,
    Template6SlideIn::ID => Template6SlideIn::class,
    Template6Widget::ID => Template6Widget::class,
    Template7BelowPages::ID => Template7BelowPages::class,
    Template7FixedBar::ID => Template7FixedBar::class,
    Template7Popup::ID => Template7Popup::class,
    Template7SlideIn::ID => Template7SlideIn::class,
    Template7Widget::ID => Template7Widget::class,
    Template10BelowPages::ID => Template10BelowPages::class,
    Template10FixedBar::ID => Template10FixedBar::class,
    Template10Popup::ID => Template10Popup::class,
    Template10SlideIn::ID => Template10SlideIn::class,
    Template10Widget::ID => Template10Widget::class,
    Template11BelowPages::ID => Template11BelowPages::class,
    Template11FixedBar::ID => Template11FixedBar::class,
    Template11Popup::ID => Template11Popup::class,
    Template11SlideIn::ID => Template11SlideIn::class,
    Template11Widget::ID => Template11Widget::class,
    Template12BelowPages::ID => Template12BelowPages::class,
    Template12FixedBar::ID => Template12FixedBar::class,
    Template12Popup::ID => Template12Popup::class,
    Template12SlideIn::ID => Template12SlideIn::class,
    Template12Widget::ID => Template12Widget::class,
    Template13BelowPages::ID => Template13BelowPages::class,
    Template13FixedBar::ID => Template13FixedBar::class,
    Template13Popup::ID => Template13Popup::class,
    Template13SlideIn::ID => Template13SlideIn::class,
    Template13Widget::ID => Template13Widget::class,
    Template14BelowPages::ID => Template14BelowPages::class,
    Template14FixedBar::ID => Template14FixedBar::class,
    Template14Popup::ID => Template14Popup::class,
    Template14SlideIn::ID => Template14SlideIn::class,
    Template14Widget::ID => Template14Widget::class,
    Template17BelowPages::ID => Template17BelowPages::class,
    Template17FixedBar::ID => Template17FixedBar::class,
    Template17Popup::ID => Template17Popup::class,
    Template17SlideIn::ID => Template17SlideIn::class,
    Template17Widget::ID => Template17Widget::class,
    Template18BelowPages::ID => Template18BelowPages::class,
    Template18FixedBar::ID => Template18FixedBar::class,
    Template18Popup::ID => Template18Popup::class,
    Template18SlideIn::ID => Template18SlideIn::class,
    Template18Widget::ID => Template18Widget::class,
  ];

  public function __construct(
    CdnAssetUrl $cdnAssetUrl,
    SettingsController $settings,
    WPFunctions $wp
  ) {
    $this->cdnAssetUrl = $cdnAssetUrl;
    $this->wp = $wp;
    $this->settings = $settings;
  }

  public function getFormTemplate(string $templateId): FormTemplate {
    if (!isset($this->templates[$templateId])) {
      throw UnexpectedValueException::create()
        ->withErrors(["Template with id $templateId doesn't exist."]);
    }
    /** @var FormTemplate $template */
    $template = new $this->templates[$templateId]($this->cdnAssetUrl, $this->settings, $this->wp);
    return $template;
  }

  /**
   * @param string[] $templateIds
   * @return FormTemplate[] associative array with template ids as keys
   */
  public function getFormTemplates(array $templateIds): array {
    $result = [];
    foreach ($templateIds as $templateId) {
      $result[$templateId] = $this->getFormTemplate($templateId);
    }
    return $result;
  }
}
