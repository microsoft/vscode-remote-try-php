<?php declare(strict_types = 1);

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\AssetsController;
use MailPoet\AdminPages\PageRenderer;
use MailPoet\Automation\Engine\Control\SubjectTransformerHandler;
use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\Mappers\AutomationMapper;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice as WPNotice;

class AutomationEditor {
  /** @var AssetsController */
  private $assetsController;

  /** @var AutomationMapper */
  private $automationMapper;

  /** @var AutomationStorage */
  private $automationStorage;

  /** @var PageRenderer */
  private $pageRenderer;

  /** @var Registry */
  private $registry;

  /** @var WPFunctions */
  private $wp;

  /** @var SubjectTransformerHandler */
  private $subjectTransformerHandler;

  public function __construct(
    AssetsController $assetsController,
    AutomationMapper $automationMapper,
    AutomationStorage $automationStorage,
    PageRenderer $pageRenderer,
    Registry $registry,
    WPFunctions $wp,
    SubjectTransformerHandler $subjectTransformerHandler
  ) {
    $this->assetsController = $assetsController;
    $this->automationMapper = $automationMapper;
    $this->automationStorage = $automationStorage;
    $this->pageRenderer = $pageRenderer;
    $this->registry = $registry;
    $this->wp = $wp;
    $this->subjectTransformerHandler = $subjectTransformerHandler;
  }

  public function render() {
    $this->assetsController->setupAutomationEditorDependencies();

    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

    $this->wp->doAction(Hooks::EDITOR_BEFORE_LOAD, (int)$id);

    $automation = $id ? $this->automationStorage->getAutomation($id) : null;
    if (!$automation) {
      $notice = new WPNotice(
        WPNotice::TYPE_ERROR,
        __('Automation not found.', 'mailpoet')
      );
      $notice->displayWPNotice();
      $this->pageRenderer->displayPage('blank.html');
      return;
    }

    if ($automation->getStatus() === Automation::STATUS_TRASH) {
      $this->wp->wpSafeRedirect($this->wp->adminUrl('admin.php?page=mailpoet-automation&status=trash&notice=had-been-deleted'));
      exit();
    }

    $this->pageRenderer->displayPage('automation/editor.html', [
      'registry' => $this->buildRegistry(),
      'context' => $this->buildContext(),
      'automation' => $this->automationMapper->buildAutomation($automation),
      'locale_full' => $this->wp->getLocale(),
      'api' => [
        'root' => rtrim($this->wp->escUrlRaw($this->wp->restUrl()), '/'),
        'nonce' => $this->wp->wpCreateNonce('wp_rest'),
      ],
      'jsonapi' => [
        'root' => rtrim($this->wp->escUrlRaw(admin_url('admin-ajax.php')), '/'),
      ],
    ]);
  }

  private function buildRegistry(): array {
    $steps = [];
    foreach ($this->registry->getSteps() as $key => $step) {
      $steps[$key] = [
        'key' => $step->getKey(),
        'name' => $step->getName(),
        'subject_keys' => $step instanceof Trigger ? $this->subjectTransformerHandler->getSubjectKeysForTrigger($step) : $step->getSubjectKeys(),
        'args_schema' => $step->getArgsSchema()->toArray(),
      ];
    }

    $subjects = [];
    foreach ($this->registry->getSubjects() as $key => $subject) {
      $subjectFields = $subject->getFields();
      usort($subjectFields, function (Field $a, Field $b) {
        return $a->getName() <=> $b->getName();
      });

      $subjects[$key] = [
        'key' => $subject->getKey(),
        'name' => $subject->getName(),
        'args_schema' => $subject->getArgsSchema()->toArray(),
        'field_keys' => array_map(function ($field) {
          return $field->getKey();
        }, $subjectFields),
      ];
    }

    $fields = [];
    foreach ($this->registry->getFields() as $key => $field) {
      $fields[$key] = [
        'key' => $field->getKey(),
        'type' => $field->getType(),
        'name' => $field->getName(),
        'args' => $field->getArgs(),
      ];
    }

    $filters = [];
    foreach ($this->registry->getFilters() as $fieldType => $filter) {
      $conditions = [];
      foreach ($filter->getConditions() as $key => $label) {
        $conditions[] = [
          'key' => $key,
          'label' => $label,
        ];
      }
      $filters[$fieldType] = [
        'field_type' => $filter->getFieldType(),
        'conditions' => $conditions,
      ];
    }

    return [
      'steps' => $steps,
      'subjects' => $subjects,
      'fields' => $fields,
      'filters' => $filters,
    ];
  }

  private function buildContext(): array {
    $data = [];
    foreach ($this->registry->getContextFactories() as $key => $factory) {
      $data[$key] = $factory();
    }
    return $data;
  }
}
