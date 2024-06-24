<?php declare(strict_types = 1);

namespace MailPoet\Mailer\Methods;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\Methods\ErrorMappers\SMTPMapper;
use MailPoet\RuntimeException;
use MailPoet\WP\Functions as WPFunctions;
use PHPMailer\PHPMailer\PHPMailer;

class SMTP extends PHPMailerMethod {
  const SMTP_CONNECTION_TIMEOUT = 15; // seconds

  /** @var string */
  public $host;
  /** @var int */
  public $port;
  /** @var int */
  public $authentication;
  /** @var string  */
  public $login;
  /** @var string */
  public $password;
  /** @var string */
  public $encryption;
  /** @var PHPMailer */
  public $mailer;
  /** @var WPFunctions */
  protected $wp;

  public function __construct(
    $host,
    $port,
    $authentication,
    $encryption,
    $sender,
    $replyTo,
    $returnPath,
    SMTPMapper $errorMapper,
    $login = null,
    $password = null
  ) {
    $this->wp = new WPFunctions;
    $this->host = $host;
    $this->port = $port;
    $this->authentication = $authentication;
    $this->login = $login;
    $this->password = $password;
    $this->encryption = $encryption;
    parent::__construct($sender, $replyTo, $returnPath, $errorMapper);
  }

  public function buildMailer(): PHPMailer {
    $mailer = new PHPMailer(true);
    $mailer->isSMTP();
    /** @phpstan-ignore-next-line - we cannot annotate the return type from a filter */
    $mailer->Host = $this->wp->applyFilters('mailpoet_mailer_smtp_host', $this->host); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    /** @phpstan-ignore-next-line - we cannot annotate the return type from a filter */
    $mailer->Port = $this->wp->applyFilters('mailpoet_mailer_smtp_port', $this->port); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    /** @phpstan-ignore-next-line - we cannot annotate the return type from a filter */
    $mailer->SMTPSecure = $this->wp->applyFilters('mailpoet_mailer_smtp_encryption', $this->encryption);
    /** @phpstan-ignore-next-line - we cannot annotate the return type from a filter */
    $mailer->SMTPOptions = $this->wp->applyFilters('mailpoet_mailer_smtp_options', []);
    /** @phpstan-ignore-next-line - we cannot annotate the return type from a filter */
    $mailer->Timeout = $this->wp->applyFilters('mailpoet_mailer_smtp_connection_timeout', self::SMTP_CONNECTION_TIMEOUT); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    if ($this->authentication === 1) {
      $mailer->SMTPAuth = true; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      $mailer->Username = $this->login; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      $mailer->Password = $this->password; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    }

    // values from filters can overwrite username and password
    $filterUsername = $this->wp->applyFilters('mailpoet_mailer_smtp_username', null);
    $filterPassword = $this->wp->applyFilters('mailpoet_mailer_smtp_password', null);
    if ($filterUsername && $filterPassword) {
      $mailer->SMTPAuth = true; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      /** @phpstan-ignore-next-line - we cannot annotate the return type from a filter */
      $mailer->Username = $filterUsername; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      /** @phpstan-ignore-next-line - we cannot annotate the return type from a filter */
      $mailer->Password = $filterPassword; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    }

    $mailer = $this->wp->applyFilters('mailpoet_mailer_smtp_instance', $mailer);
    if (!$mailer instanceof PHPMailer) {
      throw new RuntimeException(__('Filter "mailpoet_mailer_smtp_instance" must return an instance of PHPMailer.', 'mailpoet'));
    }
    return $mailer;
  }
}
