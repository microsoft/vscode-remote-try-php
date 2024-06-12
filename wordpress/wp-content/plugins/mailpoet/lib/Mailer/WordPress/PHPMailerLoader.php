<?php declare(strict_types = 1);

namespace MailPoet\Mailer\WordPress;

if (!defined('ABSPATH')) exit;


class PHPMailerLoader {
  /**
   * Load PHPMailer because is not autoloaded
   */
  public static function load(): void {
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
      require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
    }
    if (!class_exists('PHPMailer\PHPMailer\Exception')) {
      require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
    }
    if (!class_exists('PHPMailer\PHPMailer\SMTP')) {
      require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
    }
  }
}
