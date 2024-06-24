<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Mailer;

if (!defined('ABSPATH')) exit;


class SubscriberError {

  /** @var string */
  private $email;

  /** @var string|null */
  private $message;

  /**
   * @param string $email
   * @param string $message|null
   */
  public function __construct(
    $email,
    $message = null
  ) {
    $this->email = $email;
    $this->message = $message;
  }

  /**
   * @return string
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * @return null|string
   */
  public function getMessage() {
    return $this->message;
  }

  public function __toString() {
    return $this->message ? $this->email . ': ' . $this->message : $this->email;
  }
}
