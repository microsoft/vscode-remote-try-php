<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\AssetsController;
use MailPoet\AdminPages\PageRenderer;
use MailPoet\API\JSON\ResponseBuilders\CustomFieldsResponseBuilder;
use MailPoet\Config\Localizer;
use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\FormEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Form\Block;
use MailPoet\Form\FormsRepository;
use MailPoet\Form\Renderer as FormRenderer;
use MailPoet\Form\Templates\TemplateRepository;
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
use MailPoet\Form\Util\CustomFonts;
use MailPoet\Form\Util\Export;
use MailPoet\NotFoundException;
use MailPoet\Router\Endpoints\FormPreview;
use MailPoet\Router\Router;
use MailPoet\Segments\SegmentsSimpleListRepository;
use MailPoet\Settings\Pages;
use MailPoet\Settings\UserFlagsController;
use MailPoet\WP\AutocompletePostListLoader as WPPostListLoader;
use MailPoet\WP\Functions as WPFunctions;

class FormEditor {
  /** @var PageRenderer */
  private $pageRenderer;

  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  /** @var CustomFieldsResponseBuilder */
  private $customFieldsResponseBuilder;

  /** @var FormRenderer */
  private $formRenderer;

  /** @var Block\Date */
  private $dateBlock;

  /** @var WPFunctions */
  private $wp;

  /** @var Localizer */
  private $localizer;

  /** @var TemplateRepository */
  private $templatesRepository;

  /** @var UserFlagsController */
  private $userFlags;

  /** @var WPPostListLoader */
  private $wpPostListLoader;

  /** @var SegmentsSimpleListRepository */
  private $segmentsListRepository;

  /** @var FormsRepository */
  private $formsRepository;

  private $activeTemplates = [
    FormEntity::DISPLAY_TYPE_POPUP => [
      Template1Popup::ID,
      Template3Popup::ID,
      Template4Popup::ID,
      Template6Popup::ID,
      Template7Popup::ID,
      Template10Popup::ID,
      Template11Popup::ID,
      Template12Popup::ID,
      Template13Popup::ID,
      Template14Popup::ID,
      Template17Popup::ID,
      Template18Popup::ID,
    ],
    FormEntity::DISPLAY_TYPE_SLIDE_IN => [
      Template1SlideIn::ID,
      Template3SlideIn::ID,
      Template4SlideIn::ID,
      Template6SlideIn::ID,
      Template7SlideIn::ID,
      Template10SlideIn::ID,
      Template11SlideIn::ID,
      Template12SlideIn::ID,
      Template13SlideIn::ID,
      Template14SlideIn::ID,
      Template17SlideIn::ID,
      Template18SlideIn::ID,
    ],
    FormEntity::DISPLAY_TYPE_FIXED_BAR => [
      Template1FixedBar::ID,
      Template3FixedBar::ID,
      Template4FixedBar::ID,
      Template6FixedBar::ID,
      Template7FixedBar::ID,
      Template10FixedBar::ID,
      Template11FixedBar::ID,
      Template12FixedBar::ID,
      Template13FixedBar::ID,
      Template14FixedBar::ID,
      Template17FixedBar::ID,
      Template18FixedBar::ID,
    ],
    FormEntity::DISPLAY_TYPE_BELOW_POST => [
      Template1BelowPages::ID,
      Template3BelowPages::ID,
      Template4BelowPages::ID,
      Template6BelowPages::ID,
      Template7BelowPages::ID,
      Template10BelowPages::ID,
      Template11BelowPages::ID,
      Template12BelowPages::ID,
      Template13BelowPages::ID,
      Template14BelowPages::ID,
      Template17BelowPages::ID,
      Template18BelowPages::ID,
    ],
    FormEntity::DISPLAY_TYPE_OTHERS => [
      Template1Widget::ID,
      Template3Widget::ID,
      Template4Widget::ID,
      Template6Widget::ID,
      Template7Widget::ID,
      Template10Widget::ID,
      Template11Widget::ID,
      Template12Widget::ID,
      Template13Widget::ID,
      Template14Widget::ID,
      Template17Widget::ID,
      Template18Widget::ID,
    ],
  ];

  /** @var AssetsController */
  private $assetsController;

  public function __construct(
    AssetsController $assetsController,
    PageRenderer $pageRenderer,
    CustomFieldsRepository $customFieldsRepository,
    CustomFieldsResponseBuilder $customFieldsResponseBuilder,
    FormRenderer $formRenderer,
    Block\Date $dateBlock,
    WPFunctions $wp,
    Localizer $localizer,
    UserFlagsController $userFlags,
    WPPostListLoader $wpPostListLoader,
    TemplateRepository $templateRepository,
    FormsRepository $formsRepository,
    SegmentsSimpleListRepository $segmentsListRepository
  ) {
    $this->assetsController = $assetsController;
    $this->pageRenderer = $pageRenderer;
    $this->customFieldsRepository = $customFieldsRepository;
    $this->customFieldsResponseBuilder = $customFieldsResponseBuilder;
    $this->formRenderer = $formRenderer;
    $this->dateBlock = $dateBlock;
    $this->wp = $wp;
    $this->localizer = $localizer;
    $this->templatesRepository = $templateRepository;
    $this->userFlags = $userFlags;
    $this->wpPostListLoader = $wpPostListLoader;
    $this->segmentsListRepository = $segmentsListRepository;
    $this->formsRepository = $formsRepository;
  }

  public function render() {
    if (!isset($_GET['id']) && !isset($_GET['action']) && !isset($_GET['template_id'])) {
      $this->renderTemplateSelection();
      return;
    }
    if (isset($_GET['template_id'])) {
      $template = $this->templatesRepository->getFormTemplate(sanitize_text_field(wp_unslash($_GET['template_id'])));
      $form = $template->toFormEntity();
    } else {
      $form = $this->getFormData((int)$_GET['id']);
    }
    $customFields = $this->customFieldsRepository->findAll();
    if (!$form instanceof FormEntity) {
      throw new NotFoundException('Form does not exist');
    }
    $dateTypes = $this->dateBlock->getDateTypes();
    $data = [
      'form' => $form->toArray(),
      'form_exports' => [
          'php' => Export::get('php'),
          'iframe' => Export::get('iframe'),
          'shortcode' => Export::get('shortcode'),
      ],
      'segments' => $this->segmentsListRepository->getListWithSubscribedSubscribersCounts([SegmentEntity::TYPE_DEFAULT]),
      'styles' => $this->formRenderer->getCustomStyles($form),
      'date_types' => array_map(function ($label, $value) {
        return [
          'label' => $label,
          'value' => $value,
        ];
      }, $dateTypes, array_keys($dateTypes)),
      'date_formats' => $this->dateBlock->getDateFormats(),
      'month_names' => $this->dateBlock->getMonthNames(),
      'custom_fields' => $this->customFieldsResponseBuilder->buildBatch($customFields),
      'editor_tutorial_seen' => $this->userFlags->get('form_editor_tutorial_seen'),
      'preview_page_url' => $this->getPreviewPageUrl(),
      'custom_fonts' => CustomFonts::FONTS,
      'translations' => $this->getGutenbergScriptsTranslations(),
      'posts' => $this->wpPostListLoader->getPosts(),
      'pages' => $this->wpPostListLoader->getPages(),
      'categories' => $this->wpPostListLoader->getCategories(),
      'tags' => $this->wpPostListLoader->getTags(),
      'products' => $this->wpPostListLoader->getProducts(),
      'product_categories' => $this->wpPostListLoader->getWooCommerceCategories(),
      'product_tags' => $this->wpPostListLoader->getWooCommerceTags(),
      'is_administrator' => $this->wp->currentUserCan('administrator'),
    ];
    $this->wp->wpEnqueueMedia();
    $this->assetsController->setupFormEditorDependencies();
    $this->pageRenderer->displayPage('form/editor.html', $data);
  }

  public function renderTemplateSelection() {
    $templatesData = [];
    foreach ($this->activeTemplates as $formType => $templateIds) {
      $templateForms = $this->templatesRepository->getFormTemplates($this->activeTemplates[$formType]);
      $templatesData[$formType] = [];
      foreach ($templateForms as $templateId => $form) {
        $templatesData[$formType][] = [
          'id' => $templateId,
          'name' => $form->getName(),
          'thumbnail' => $form->getThumbnailUrl(),
        ];
      }
    }
    $data = [
      'templates' => $templatesData,
    ];
    $this->assetsController->setupFormEditorDependencies();
    $this->pageRenderer->displayPage('form/template_selection.html', $data);
  }

  private function getPreviewPageUrl() {
    $mailpoetPage = Pages::getDefaultMailPoetPage();
    if (!$mailpoetPage) {
      return null;
    }
    $url = $this->wp->getPermalink($mailpoetPage);
    $params = [
      Router::NAME,
      'endpoint=' . FormPreview::ENDPOINT,
      'action=' . FormPreview::ACTION_VIEW,
    ];
    $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . join('&', $params);
    return $url;
  }

  /**
   * JS Translations are distributed and loaded per script. We can't use wp_set_script_translations
   * because translation filename is determined based on script filename and path.
   * This function loads JSON files with Gutenberg script's translations distributed within WordPress.
   * Implemented based on load_script_textdomain function
   * @see https://developer.wordpress.org/reference/functions/load_script_textdomain/
   * @return string[]
   */
  private function getGutenbergScriptsTranslations() {
    $locale = $this->localizer->locale();
    if (!$locale) {
      return [];
    }
    // List of scripts - relative path to translations directory (default: wp-content/languages)
    $translationsToLoad = [
      'wp-includes/js/dist/blocks.js',
      'wp-includes/js/dist/components.js',
      'wp-includes/js/dist/block-editor.js',
      'wp-includes/js/dist/block-library.js',
      'wp-includes/js/dist/editor.js',
      'wp-includes/js/dist/media-utils.js',
      'wp-includes/js/dist/format-library.js',
      'wp-includes/js/dist/edit-post.js',
    ];

    $translations = [];
    foreach ($translationsToLoad as $translation) {
      $file = WP_LANG_DIR . '/' . $locale . '-' . md5($translation) . '.json';
      if (!file_exists($file)) {
        continue;
      }
      $translationsData = file_get_contents($file);
      if ($translationsData) {
        $translations[] = $translationsData;
      }
    }
    return $translations;
  }

  private function getFormData(int $id): ?FormEntity {
    $form = $this->formsRepository->findOneById($id);
    if (!$form instanceof FormEntity) {
      return null;
    }
    $form->setStyles($this->formRenderer->getCustomStyles($form));
    // Use empty settings in case they are corrupted or missing
    if (!is_array($form->getSettings())) {
      $initialFormTemplate = $this->templatesRepository->getFormTemplate(TemplateRepository::INITIAL_FORM_TEMPLATE);
      $form->setSettings($initialFormTemplate->getSettings());
    }
    return $form;
  }
}
