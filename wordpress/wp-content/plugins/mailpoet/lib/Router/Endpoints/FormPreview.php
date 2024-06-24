<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Router\Endpoints;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\AccessControl;
use MailPoet\Form\PreviewPage;
use MailPoet\WP\Functions as WPFunctions;

class FormPreview {
  const ENDPOINT = 'form_preview';
  const ACTION_VIEW = 'view';

  /** @var WPFunctions  */
  private $wp;

  /** @var array|null */
  private $data;

  /** @var PreviewPage */
  private $formPreviewPage;

  public $allowedActions = [self::ACTION_VIEW];
  public $permissions = [
    'global' => AccessControl::NO_ACCESS_RESTRICTION,
  ];

  public function __construct(
    WPFunctions $wp,
    PreviewPage $formPreviewPage
  ) {
    $this->wp = $wp;
    $this->formPreviewPage = $formPreviewPage;
  }

  public function view(array $data) {
    $this->data = $data;
    $this->wp->addFilter('the_content', [$this, 'renderContent'], 10);
    $this->wp->addFilter('the_title', [$this->formPreviewPage, 'renderTitle'], 10, 2);
    $this->wp->addFilter('show_admin_bar', function () {
      return false;
    });
  }

  public function renderContent(): string {
    if (!array_key_exists('id', $this->data ?? []) || !isset($this->data['form_type'])) {
      return '';
    }
    return $this->formPreviewPage->renderPage(
      (int)$this->data['id'],
      (string)$this->data['form_type'],
      (string)$this->data['editor_url']
    );
  }
}
