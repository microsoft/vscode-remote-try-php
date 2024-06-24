<?php declare(strict_types = 1);

namespace MailPoet\Services;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

/**
 * This class contains validation methods that were extracted from the \MailPoet\Models\ModelValidator class.
 * It is used only in a few places and there is a chance in the future we can remove it.
 */
class Validator {
  const EMAIL_MIN_LENGTH = 6;
  const EMAIL_MAX_LENGTH = 150;
  const ROLE_EMAILS = [
    'abuse',
    'compliance',
    'devnull',
    'dns',
    'ftp',
    'hostmaster',
    'inoc',
    'ispfeedback',
    'ispsupport',
    'list-request',
    'list',
    'maildaemon',
    'noc',
    'no-reply',
    'noreply',
    'nospam',
    'null',
    'phish',
    'phishing',
    'postmaster',
    'privacy',
    'registrar',
    'root',
    'security',
    'spam',
    'sysadmin',
    'undisclosed-recipients',
    'unsubscribe',
    'usenet',
    'uucp',
    'webmaster',
    'www',
  ];

  public function validateEmail($email): bool {
    $permittedLength = (strlen($email) >= self::EMAIL_MIN_LENGTH && strlen($email) <= self::EMAIL_MAX_LENGTH);
    $validEmail = WPFunctions::get()->isEmail($email) !== false && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    return ($permittedLength && $validEmail);
  }

  public function validateNonRoleEmail($email): bool {
    if (!$this->validateEmail($email)) return false;
    $firstPart = strtolower(substr($email, 0, (int)strpos($email, '@')));
    return array_search($firstPart, self::ROLE_EMAILS) === false;
  }
}
