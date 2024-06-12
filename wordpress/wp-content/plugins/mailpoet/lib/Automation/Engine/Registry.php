<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\RootStep;
use MailPoet\Automation\Engine\Data\AutomationTemplate;
use MailPoet\Automation\Engine\Data\AutomationTemplateCategory;
use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Automation\Engine\Integration\Filter;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Step;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Engine\Integration\SubjectTransformer;
use MailPoet\Automation\Engine\Integration\Trigger;

class Registry {
  /** @var array<string, AutomationTemplate> */
  private $templates;

  /** @var array<string, AutomationTemplateCategory> */
  private $templateCategories;

  /** @var array<string, Step> */
  private $steps = [];

  /** @var array<string, Subject<Payload>> */
  private $subjects = [];

  /** @var SubjectTransformer[] */
  private $subjectTransformers = [];

  /** @var array<string, Field>|null */
  private $fields = null;

  /** @var array<string, Filter> */
  private $filters = [];

  /** @var array<string, Trigger> */
  private $triggers = [];

  /** @var array<string, Action> */
  private $actions = [];

  /** @var array<string, callable> */
  private $contextFactories = [];

  /** @var WordPress */
  private $wordPress;

  public function __construct(
    RootStep $rootStep,
    WordPress $wordPress
  ) {
    $this->wordPress = $wordPress;
    $this->steps[$rootStep->getKey()] = $rootStep;

    $this->templateCategories = [
      'welcome' => new AutomationTemplateCategory('welcome', __('Welcome', 'mailpoet')),
      'abandoned-cart' => new AutomationTemplateCategory('abandoned-cart', __('Abandoned Cart', 'mailpoet')),
      'reengagement' => new AutomationTemplateCategory('reengagement', __('Re-engagement', 'mailpoet')),
      'woocommerce' => new AutomationTemplateCategory('woocommerce', __('WooCommerce', 'mailpoet')),
    ];
  }

  public function addTemplate(AutomationTemplate $template): void {
    $category = $template->getCategory();
    if (!isset($this->templateCategories[$category])) {
      throw InvalidStateException::create()->withMessage(
        sprintf("Category '%s' was not registered", $category)
      );
    }

    $this->templates[$template->getSlug()] = $template;

    // keep coming soon templates at the end
    uasort(
      $this->templates,
      function (AutomationTemplate $a, AutomationTemplate $b): int {
        if ($a->getType() === AutomationTemplate::TYPE_COMING_SOON) {
          return 1;
        }
        if ($b->getType() === AutomationTemplate::TYPE_COMING_SOON) {
          return -1;
        }
        return 0;
      }
    );
  }

  public function getTemplate(string $slug): ?AutomationTemplate {
    return $this->getTemplates()[$slug] ?? null;
  }

  /** @return array<string, AutomationTemplate> */
  public function getTemplates(string $category = null): array {
    return $category
      ? array_filter(
        $this->templates,
        function(AutomationTemplate $template) use ($category): bool {
          return $template->getCategory() === $category;
        }
      )
      : $this->templates;
  }

  public function removeTemplate(string $slug): void {
    unset($this->templates[$slug]);
  }

  /** @return array<string, AutomationTemplateCategory> */
  public function getTemplateCategories(): array {
    return $this->templateCategories;
  }

  /** @param Subject<Payload> $subject */
  public function addSubject(Subject $subject): void {
    $key = $subject->getKey();
    if (isset($this->subjects[$key])) {
      throw new \Exception(); // TODO
    }
    $this->subjects[$key] = $subject;

    // reset fields cache
    $this->fields = null;
  }

  /** @return Subject<Payload>|null */
  public function getSubject(string $key): ?Subject {
    return $this->subjects[$key] ?? null;
  }

  /** @return array<string, Subject<Payload>> */
  public function getSubjects(): array {
    return $this->subjects;
  }

  public function addSubjectTransformer(SubjectTransformer $transformer): void {
    $this->subjectTransformers[] = $transformer;
  }

  public function getSubjectTransformers(): array {
    return $this->subjectTransformers;
  }

  public function getField(string $key): ?Field {
    return $this->getFields()[$key] ?? null;
  }

  /** @return array<string, Field> */
  public function getFields(): array {
    // add fields lazily (on the first call)
    if ($this->fields === null) {
      $this->fields = [];
      foreach ($this->subjects as $subject) {
        foreach ($subject->getFields() as $field) {
          $this->addField($field);
        }
      }
    }
    return $this->fields ?? [];
  }

  public function addFilter(Filter $filter): void {
    $fieldType = $filter->getFieldType();
    if (isset($this->filters[$fieldType])) {
      throw new \Exception(); // TODO
    }
    $this->filters[$fieldType] = $filter;
  }

  public function getFilter(string $fieldType): ?Filter {
    return $this->filters[$fieldType] ?? null;
  }

  /** @return array<string, Filter> */
  public function getFilters(): array {
    return $this->filters;
  }

  public function addStep(Step $step): void {
    if ($step instanceof Trigger) {
      $this->addTrigger($step);
    } elseif ($step instanceof Action) {
      $this->addAction($step);
    }

    // TODO: allow adding any other step implementations?
  }

  public function getStep(string $key): ?Step {
    return $this->steps[$key] ?? null;
  }

  /** @return array<string, Step> */
  public function getSteps(): array {
    return $this->steps;
  }

  public function addTrigger(Trigger $trigger): void {
    $key = $trigger->getKey();
    if (isset($this->steps[$key]) || isset($this->triggers[$key])) {
      throw new \Exception(); // TODO
    }
    $this->steps[$key] = $trigger;
    $this->triggers[$key] = $trigger;
  }

  public function getTrigger(string $key): ?Trigger {
    return $this->triggers[$key] ?? null;
  }

  /** @return array<string, Trigger> */
  public function getTriggers(): array {
    return $this->triggers;
  }

  public function addAction(Action $action): void {
    $key = $action->getKey();
    if (isset($this->steps[$key]) || isset($this->actions[$key])) {
      throw new \Exception(); // TODO
    }
    $this->steps[$key] = $action;
    $this->actions[$key] = $action;
  }

  public function getAction(string $key): ?Action {
    return $this->actions[$key] ?? null;
  }

  /** @return array<string, Action> */
  public function getActions(): array {
    return $this->actions;
  }

  public function addContextFactory(string $key, callable $factory): void {
    $this->contextFactories[$key] = $factory;
  }

  /** @return callable[] */
  public function getContextFactories(): array {
    return $this->contextFactories;
  }

  public function onBeforeAutomationSave(callable $callback, int $priority = 10): void {
    $this->wordPress->addAction(Hooks::AUTOMATION_BEFORE_SAVE, $callback, $priority);
  }

  public function onBeforeAutomationStepSave(callable $callback, string $key = null, int $priority = 10): void {
    $keyPart = $key ? "/key=$key" : '';
    $this->wordPress->addAction(Hooks::AUTOMATION_STEP_BEFORE_SAVE . $keyPart, $callback, $priority, 2);
  }

  /**
   * This is used only internally. Fields are added lazily from subjects.
   */
  private function addField(Field $field): void {
    $key = $field->getKey();
    if (isset($this->fields[$key])) {
      throw new \Exception(); // TODO
    }
    $this->fields[$key] = $field;
  }
}
