<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Router\Endpoints;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\AccessControl;
use MailPoet\Newsletter\ViewInBrowser\ViewInBrowserController;
use MailPoet\WP\Functions as WPFunctions;

class ViewInBrowser {
  const ENDPOINT = 'view_in_browser';
  const ACTION_VIEW = 'view';

  public $allowedActions = [self::ACTION_VIEW];
  public $permissions = [
    'global' => AccessControl::NO_ACCESS_RESTRICTION,
  ];

  /** @var ViewInBrowserController */
  private $viewInBrowserController;

  public function __construct(
    ViewInBrowserController $viewInBrowserController
  ) {
    $this->viewInBrowserController = $viewInBrowserController;
  }

  public function view(array $data) {
    try {
      $viewData = $this->viewInBrowserController->view($data);
      $this->displayNewsletter($viewData);
    } catch (\InvalidArgumentException $e) {
      $this->abort();
    }
  }

  private function displayNewsletter($result) {
    header('Content-Type: text/html; charset=utf-8');
    // phpcs:ignore WordPressDotOrg.sniffs.OutputEscaping.UnescapedOutputParameter,WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $result;
    exit;
  }

  private function abort() {
    global $wp_query;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    WPFunctions::get()->statusHeader(404);
    $wp_query->set_404();// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    exit;
  }
}
