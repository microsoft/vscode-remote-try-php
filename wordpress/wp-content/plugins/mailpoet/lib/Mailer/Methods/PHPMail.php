<?php declare(strict_types = 1);

namespace MailPoet\Mailer\Methods;

if (!defined('ABSPATH')) exit;


use PHPMailer\PHPMailer\PHPMailer;

class PHPMail extends PHPMailerMethod {
  public function buildMailer(): PHPMailer {
    $mailer = new PHPMailer(true);
    // send using PHP's mail() function
    $mailer->isMail();
    return $mailer;
  }
}
