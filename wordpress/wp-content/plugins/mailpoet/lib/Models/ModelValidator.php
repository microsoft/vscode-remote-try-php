<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Models;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Services\Validator;
use MailPoet\Util\Helpers;

/**
 * @deprecated This class is deprecated. Use \MailPoet\Services\Validator instead. This class can be removed after 2024-05-30.
 */
class ModelValidator extends \MailPoetVendor\Sudzy\Engine {
  public $validators;

  /**
   * @deprecated
   */
  public function __construct() {
    self::deprecationError(__METHOD__);
    parent::__construct();
    $this->validators = [
      'validEmail' => 'validateEmail',
      'validRenderedNewsletterBody' => 'validateRenderedNewsletterBody',
    ];
    $this->setupValidators();
  }

  /**
   * @deprecated
   */
  private function setupValidators() {
    self::deprecationError(__METHOD__);
    $_this = $this;
    foreach ($this->validators as $validator => $action) {
      $this->addValidator($validator, function($params) use ($action, $_this) {
        $callback = [$_this, $action];
        if (is_callable($callback)) {
          return call_user_func($callback, $params);
        }
      });
    }
  }

  /**
   * @deprecated
   */
  public function validateEmail($email) {
    self::deprecationError(__METHOD__);
    $validator = ContainerWrapper::getInstance()->get(Validator::class);
    return $validator->validateEmail($email);
  }

  /**
   * @deprecated
   */
  public function validateRenderedNewsletterBody($newsletterBody) {
    self::deprecationError(__METHOD__);
    if (is_serialized($newsletterBody)) {
      $newsletterBody = unserialize($newsletterBody);
    } else if (Helpers::isJson($newsletterBody)) {
      $newsletterBody = json_decode($newsletterBody, true);
    }
    return (is_null($newsletterBody) || (is_array($newsletterBody) && !empty($newsletterBody['html']) && !empty($newsletterBody['text'])));
  }

  private static function deprecationError($methodName) {
    trigger_error(
      'Calling ' . esc_html($methodName) . ' is deprecated and will be removed. Use \MailPoet\Services\Validator instead.',
      E_USER_DEPRECATED
    );
  }
}
