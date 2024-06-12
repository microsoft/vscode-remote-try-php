<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\AssetsController;
use MailPoet\AdminPages\PageRenderer;
use MailPoet\Automation\Engine\Data\AutomationTemplate;
use MailPoet\Automation\Engine\Data\AutomationTemplateCategory;
use MailPoet\Automation\Engine\Registry;
use MailPoet\WP\Functions as WPFunctions;

class AutomationTemplates {
  /** @var AssetsController */
  private $assetsController;

  /** @var PageRenderer */
  private $pageRenderer;

  /** @var Registry  */
  private $registry;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    AssetsController $assetsController,
    PageRenderer $pageRenderer,
    Registry $registry,
    WPFunctions $wp
  ) {
    $this->assetsController = $assetsController;
    $this->pageRenderer = $pageRenderer;
    $this->registry = $registry;
    $this->wp = $wp;
  }

  public function render() {
    $this->assetsController->setupAutomationTemplatesDependencies();

    $this->pageRenderer->displayPage(
      'automation/templates.html',
      [
        'locale_full' => $this->wp->getLocale(),
        'api' => [
          'root' => rtrim($this->wp->escUrlRaw($this->wp->restUrl()), '/'),
          'nonce' => $this->wp->wpCreateNonce('wp_rest'),
        ],
        'templates' => array_map(
          function(AutomationTemplate $template): array {
            return $template->toArray();
          },
          array_values($this->registry->getTemplates())
        ),
        'template_categories' => array_map(
          function (AutomationTemplateCategory $category): array {
            return [
              'slug' => $category->getSlug(),
              'name' => $category->getName(),
            ];
          },
          array_values($this->registry->getTemplateCategories())
        ),
        'registry' => $this->buildRegistry(),
        'context' => $this->buildContext(),
      ]
    );
  }

  private function buildRegistry(): array {
    $steps = [];
    foreach ($this->registry->getSteps() as $key => $step) {
      $steps[$key] = [
        'key' => $step->getKey(),
        'name' => $step->getName(),
        'args_schema' => $step->getArgsSchema()->toArray(),
      ];
    }

    $subjects = [];
    foreach ($this->registry->getSubjects() as $key => $subject) {
      $subjects[$key] = [
        'key' => $subject->getKey(),
        'name' => $subject->getName(),
        'args_schema' => $subject->getArgsSchema()->toArray(),
        'field_keys' => array_map(function ($field) {
          return $field->getKey();
        }, $subject->getFields()),
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
