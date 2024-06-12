<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use Exception;
use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\API\JSON\Response;
use MailPoet\API\JSON\ResponseBuilders\FormsResponseBuilder;
use MailPoet\API\JSON\SuccessResponse;
use MailPoet\Config\AccessControl;
use MailPoet\Entities\FormEntity;
use MailPoet\Form\ApiDataSanitizer;
use MailPoet\Form\DisplayFormInWPContent;
use MailPoet\Form\FormSaveController;
use MailPoet\Form\FormsRepository;
use MailPoet\Form\Listing\FormListingRepository;
use MailPoet\Form\PreviewPage;
use MailPoet\Form\Templates\TemplateRepository;
use MailPoet\Listing;
use MailPoet\Settings\UserFlagsController;
use MailPoet\Tags\TagRepository;
use MailPoet\UnexpectedValueException;
use MailPoet\WP\Emoji;
use MailPoet\WP\Functions as WPFunctions;

class Forms extends APIEndpoint {
  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_FORMS,
  ];

  /** @var Listing\Handler */
  private $listingHandler;

  /** @var UserFlagsController */
  private $userFlags;

  /** @var FormsResponseBuilder */
  private $formsResponseBuilder;

  /** @var WPFunctions */
  private $wp;

  /** @var FormsRepository */
  private $formsRepository;

  /** @var TemplateRepository */
  private $templateRepository;

  /** @var FormListingRepository */
  private $formListingRepository;

  /** @var Emoji */
  private $emoji;

  /** @var ApiDataSanitizer */
  private $dataSanitizer;

  /** @var TagRepository */
  private $tagRepository;

  /** @var FormSaveController */
  private $formSaveController;

  public function __construct(
    Listing\Handler $listingHandler,
    UserFlagsController $userFlags,
    FormsRepository $formsRepository,
    TemplateRepository $templateRepository,
    FormListingRepository $formListingRepository,
    FormsResponseBuilder $formsResponseBuilder,
    WPFunctions $wp,
    Emoji $emoji,
    ApiDataSanitizer $dataSanitizer,
    TagRepository $tagRepository,
    FormSaveController $formSaveController
  ) {
    $this->listingHandler = $listingHandler;
    $this->userFlags = $userFlags;
    $this->wp = $wp;
    $this->formsRepository = $formsRepository;
    $this->templateRepository = $templateRepository;
    $this->formListingRepository = $formListingRepository;
    $this->formsResponseBuilder = $formsResponseBuilder;
    $this->emoji = $emoji;
    $this->dataSanitizer = $dataSanitizer;
    $this->tagRepository = $tagRepository;
    $this->formSaveController = $formSaveController;
  }

  public function get($data = []) {
    $id = (isset($data['id']) ? (int)$data['id'] : false);
    $form = $this->formsRepository->findOneById($id);
    if ($form instanceof FormEntity) {
      return $this->successResponse($this->formsResponseBuilder->build($form));
    }
    return $this->errorResponse([
      APIError::NOT_FOUND => __('This form does not exist.', 'mailpoet'),
    ]);
  }

  public function setStatus($data = []) {
    $status = (isset($data['status']) ? $data['status'] : null);

    if (!$status) {
      return $this->badRequest([
        APIError::BAD_REQUEST => __('You need to specify a status.', 'mailpoet'),
      ]);
    }

    $id = (isset($data['id'])) ? (int)$data['id'] : false;
    $form = $this->formsRepository->findOneById($id);

    if (!$form instanceof FormEntity) {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This form does not exist.', 'mailpoet'),
      ]);
    }

    if (!in_array($status, [FormEntity::STATUS_ENABLED, FormEntity::STATUS_DISABLED])) {
      return $this->badRequest([
        APIError::BAD_REQUEST =>
          sprintf(
          // translators: %1$s is a comma-seperated list of allowed values, %2$s the status the user specified.
            __('Invalid status. Allowed values are (%1$s), you specified %2$s', 'mailpoet'),
            join(', ', [FormEntity::STATUS_ENABLED, FormEntity::STATUS_DISABLED]),
            $status
          ),
      ]);
    }

    $form->setStatus($status);
    $this->formsRepository->flush();

    if ($status === FormEntity::STATUS_ENABLED) {
      $this->wp->deleteTransient(DisplayFormInWPContent::NO_FORM_TRANSIENT_KEY);
    }

    $form = $this->formsRepository->findOneById($id);
    if (!$form instanceof FormEntity) return $this->errorResponse();
    return $this->successResponse(
      $form->toArray()
    );
  }

  public function listing($data = []) {
    $data['sort_order'] = $data['sort_order'] ?? 'desc';
    $data['sort_by'] = $data['sort_by'] ?? 'updatedAt';

    $definition = $this->listingHandler->getListingDefinition($data);
    $items = $this->formListingRepository->getData($definition);
    $count = $this->formListingRepository->getCount($definition);
    $filters = $this->formListingRepository->getFilters($definition);
    $groups = $this->formListingRepository->getGroups($definition);

    return $this->successResponse($this->formsResponseBuilder->buildForListing($items), [
      'count' => $count,
      'filters' => $filters,
      'groups' => $groups,
    ]);
  }

  public function previewEditor($data = []) {
    // We want to allow preview for unsaved forms
    $formId = $data['id'] ?? 0;
    $this->wp->setTransient(PreviewPage::PREVIEW_DATA_TRANSIENT_PREFIX . $formId, $data, PreviewPage::PREVIEW_DATA_EXPIRATION);
    return $this->successResponse();
  }

  public function saveEditor($data = []) {
    $formId = (isset($data['id']) ? (int)$data['id'] : 0);
    $initialForm = $this->getFormTemplateData(TemplateRepository::INITIAL_FORM_TEMPLATE);
    $name = ($data['name'] ?? __('New form', 'mailpoet'));
    $body = ($data['body'] ?? $initialForm['body']);
    $body = $this->dataSanitizer->sanitizeBody($body);
    $settings = ($data['settings'] ?? $initialForm['settings']);
    $styles = ($data['styles'] ?? $initialForm['styles']);
    $status = ($data['status'] ?? FormEntity::STATUS_ENABLED);

    // check if the form is used as a widget
    $isWidget = false;
    $widgets = $this->wp->getOption('widget_mailpoet_form');
    if (!empty($widgets)) {
      foreach ($widgets as $widget) {
        if (isset($widget['form']) && (int)$widget['form'] === $formId) {
          $isWidget = true;
          break;
        }
      }
    }

    // Reset no form cache
    $this->wp->deleteTransient(DisplayFormInWPContent::NO_FORM_TRANSIENT_KEY);

    // check if the user gets to pick his own lists
    // or if it's selected by the admin
    $formEntity = new FormEntity($name);
    $formEntity->setBody($body);
    $listSelection = $formEntity->getSegmentBlocksSegmentIds();

    // check list selection
    if (count($listSelection)) {
      $settings['segments_selected_by'] = 'user';
      $settings['segments'] = $listSelection;
    } else {
      $settings['segments_selected_by'] = 'admin';
    }

    // check tags and create them if they don't exist
    if (isset($settings['tags'])) {
      $this->createTagsIfDoNotExist($settings['tags']);
    }

    // Check Custom HTML block permissions
    $customHtmlBlocks = $formEntity->getBlocksByTypes([FormEntity::HTML_BLOCK_TYPE]);
    if (count($customHtmlBlocks) && !$this->wp->currentUserCan('administrator')) {
      return $this->errorResponse([
        Error::FORBIDDEN => __('Only administrator can edit forms containing Custom HTML block.', 'mailpoet'),
      ], [], Response::STATUS_FORBIDDEN);
    }

    if ($body !== null) {
      $body = $this->emoji->sanitizeEmojisInFormBody($body);
    }

    $form = $this->getForm($data);

    if (!$form instanceof FormEntity) {
      $form = new FormEntity($name);
    }
    $form->setName($name);
    $form->setBody($body);
    $form->setSettings($settings);
    $form->setStyles($styles);
    $form->setStatus($status);
    $this->formsRepository->persist($form);

    try {
      $this->formsRepository->flush();
    } catch (\Exception $e) {
      return $this->badRequest();
    }

    if (isset($data['editor_version']) && $data['editor_version'] === "2") {
      $this->userFlags->set('display_new_form_editor_nps_survey', true);
    }

    $form = $this->getForm(['id' => $form->getId()]);
    if(!$form instanceof FormEntity) return $this->errorResponse();
    return $this->successResponse(
      $this->formsResponseBuilder->build($form),
      ['is_widget' => $isWidget]
    );
  }

  public function restore($data = []) {
    $form = $this->getForm($data);

    if ($form instanceof FormEntity) {
      $this->formsRepository->restore($form);
      return $this->successResponse(
        $form->toArray(),
        ['count' => 1]
      );
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This form does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function trash($data = []) {
    $form = $this->getForm($data);

    if ($form instanceof FormEntity) {
      $this->formsRepository->trash($form);
      return $this->successResponse(
        $form->toArray(),
        ['count' => 1]
      );
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This form does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function delete($data = []) {
    $form = $this->getForm($data);

    if ($form instanceof FormEntity) {
      $this->formsRepository->delete($form);

      return $this->successResponse(null, ['count' => 1]);
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This form does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function duplicate($data = []) {
    $form = $this->getForm($data);

    if ($form instanceof FormEntity) {
      try {
        $duplicate = $this->formSaveController->duplicate($form);
      } catch (Exception $e) {
        return $this->errorResponse([
          APIError::UNKNOWN => __('Duplicating form failed.', 'mailpoet'),
        ], [], Response::STATUS_UNKNOWN);
      }
      return $this->successResponse(
        $this->formsResponseBuilder->build($duplicate),
        ['count' => 1]
      );
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This form does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function bulkAction($data = []): SuccessResponse {
    $definition = $this->listingHandler->getListingDefinition($data['listing']);
    $ids = $this->formListingRepository->getActionableIds($definition);
    if ($data['action'] === 'trash') {
      $this->formsRepository->bulkTrash($ids);
    } elseif ($data['action'] === 'restore') {
      $this->formsRepository->bulkRestore($ids);
    } elseif ($data['action'] === 'delete') {
      $this->formsRepository->bulkDelete($ids);
    } else {
      throw UnexpectedValueException::create()
        ->withErrors([APIError::BAD_REQUEST => "Invalid bulk action '{$data['action']}' provided."]);
    }
    return $this->successResponse(null, ['count' => count($ids)]);
  }

  private function getForm(array $data): ?FormEntity {
    return isset($data['id'])
      ? $this->formsRepository->findOneById((int)$data['id'])
      : null;
  }

  private function getFormTemplateData(string $templateId): array {
    $formTemplate = $this->templateRepository->getFormTemplate($templateId);
    $form = $formTemplate->toFormEntity();
    return $form->toArray();
  }

  private function createTagsIfDoNotExist(array $tagNames): void {
    foreach ($tagNames as $tagName) {
      $this->tagRepository->createOrUpdate(['name' => $tagName]);
    }
  }
}
