<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Renderer as TemplateRenderer;
use MailPoet\Entities\FormEntity;
use MailPoet\WP\Functions as WPFunctions;

class PreviewPage {
  const PREVIEW_DATA_TRANSIENT_PREFIX = 'mailpoet_form_preview_';
  const PREVIEW_DATA_EXPIRATION = 84600; // 1 DAY

  /** @var WPFunctions  */
  private $wp;

  /** @var Renderer */
  private $formRenderer;

  /** @var TemplateRenderer */
  private $templateRenderer;

  /** @var FormsRepository */
  private $formRepository;

  /** @var AssetsController */
  private $assetsController;

  public function __construct(
    WPFunctions $wp,
    Renderer $formRenderer,
    TemplateRenderer $templateRenderer,
    FormsRepository $formRepository,
    AssetsController $assetsController
  ) {
    $this->wp = $wp;
    $this->formRenderer = $formRenderer;
    $this->templateRenderer = $templateRenderer;
    $this->formRepository = $formRepository;
    $this->assetsController = $assetsController;
  }

  public function renderPage(int $formId, string $formType, string $editorUrl): string {
    $this->assetsController->setupFormPreviewDependencies();
    $formData = $this->fetchFormData($formId);
    if (!$formData instanceof FormEntity) {
      return '';
    }
    return $this->templateRenderer->render(
      'form/form_preview.html',
      [
        'post' => $this->getPostContent(),
        'form' => $this->getFormContent($formData, $formId, $formType, $editorUrl),
        'formType' => $formType,
      ]
    );
  }

  public function renderTitle($title = null, $id = null) {
    if ($id !== $this->wp->getTheId()) {
      return $title;
    }
    return __('Sample page to preview your form', 'mailpoet');
  }

  private function fetchFormData(int $id): ?FormEntity {
    $formData = $this->wp->getTransient(self::PREVIEW_DATA_TRANSIENT_PREFIX . $id);
    if (is_array($formData)) {
      $form = new FormEntity($formData['name']);
      $form->setId($formData['id'] ?? 0);
      $form->setBody($formData['body']);
      $form->setSettings($formData['settings']);
      $form->setStyles($formData['styles']);
      $form->setStatus($formData['status']);
      return $form;
    }
    return $this->formRepository->findOneById($id);
  }

  private function getFormContent(FormEntity $form, int $formId, string $formDisplayType, string $editorUrl): string {
    $settings = $form->getSettings();
    $htmlId = 'mailpoet_form_preview_' . $formId;
    $templateData = [
      'is_preview' => true,
      'editor_url' => $editorUrl,
      'form_html_id' => $htmlId,
      'form_id' => $formId,
      'form_success_message' => $settings['success_message'] ?? null,
      'form_type' => $formDisplayType,
      'close_button_icon' => $settings['close_button'] ?? 'classic',
      'styles' => $this->formRenderer->renderStyles($form, '#' . $htmlId, $formDisplayType),
      'html' => $this->formRenderer->renderHTML($form),
      'success' => false,
      'error' => false,
      'delay' => 1,
      'position' => $settings['form_placement'][$formDisplayType]['position'] ?? '',
      'animation' => $settings['form_placement'][$formDisplayType]['animation'] ?? '',
      'fontFamily' => $settings['font_family'] ?? '',
    ];
    $formPosition = $settings['form_placement'][$formDisplayType]['position'] ?? '';
    if (!$formPosition && $formDisplayType === FormEntity::DISPLAY_TYPE_FIXED_BAR) {
      $formPosition = 'top';
    }
    if (!$formPosition && $formDisplayType === FormEntity::DISPLAY_TYPE_SLIDE_IN) {
      $formPosition = 'right';
    }
    $templateData['position'] = $formPosition;
    return $this->templateRenderer->render('form/front_end_form.html', $templateData);
  }

  private function getPostContent(): string {
    $posts = $this->wp->getPosts([
      'numberposts' => 1,
      'orderby' => 'date',
      'order' => 'DESC',
      'post_status' => 'publish',
      'post_type' => 'post',
    ]);
    if (!isset($posts[0])) {
      return '';
    }
    return $posts[0]->post_content;
  }
}
