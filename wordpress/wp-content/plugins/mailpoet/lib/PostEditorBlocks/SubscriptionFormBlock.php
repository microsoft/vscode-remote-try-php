<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\PostEditorBlocks;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\FormEntity;
use MailPoet\Form\FormsRepository;
use MailPoet\Form\Widget;
use MailPoet\WP\Functions as WPFunctions;

// phpcs:disable Generic.Files.InlineHTML
class SubscriptionFormBlock {
  /** @var WPFunctions */
  private $wp;

  /** @var FormsRepository */
  private $formsRepository;

  public function __construct(
    WPFunctions $wp,
    FormsRepository $formsRepository
  ) {
    $this->wp = $wp;
    $this->formsRepository = $formsRepository;
  }

  public function init() {
    $this->wp->registerBlockType('mailpoet/subscription-form-block-render', [
      'attributes' => [
        'formId' => [
          'type' => 'number',
          'default' => null,
        ],
      ],
      'render_callback' => [$this, 'renderForm'],
    ]);
  }

  public function initAdmin() {
    $this->wp->registerBlockType('mailpoet/subscription-form-block', [
      'style' => 'mailpoetblock-form-block-css',
      'editor_script' => 'mailpoet/subscription-form-block',
    ]);

    $this->wp->addAction('admin_head', function() {
      $forms = $this->formsRepository->findAllNotDeleted();
      $form_json = wp_json_encode(
        array_map(
          function(FormEntity $form) {
            return $form->toArray();
          },
          $forms
        )
      );
      ?>
      <script type="text/javascript">
        window.mailpoet_forms = <?php echo $form_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;
        window.locale = {
          selectForm: '<?php echo esc_js(__('Select a MailPoet form', 'mailpoet')) ?>',
          createForm: '<?php echo esc_js(__('Create a new form', 'mailpoet')) ?>',
          subscriptionForm: '<?php echo esc_js(__('MailPoet Subscription Form', 'mailpoet')) ?>',
          inactive: '<?php echo esc_js(__('inactive', 'mailpoet')) ?>',
        };
      </script>
      <?php
    });
  }

  public function initFrontend() {
    $this->wp->registerBlockType('mailpoet/subscription-form-block', [
      'render_callback' => [$this, 'renderForm'],
    ]);
  }

  public function renderForm(array $attributes = []): string {
    if (!$attributes || !isset($attributes['formId'])) {
      return '';
    }
    $basicForm = new Widget();
    return $basicForm->widget([
      'form' => (int)$attributes['formId'],
      'form_type' => 'html',
    ]);
  }
}
