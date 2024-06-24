<?php declare(strict_types = 1);

namespace MailPoet\Mailer\Methods\ErrorMappers;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\Mailer;

class PHPMailMapper extends PHPMailerMapper {
  use BlacklistErrorMapperTrait;

  public const METHOD = Mailer::METHOD_PHPMAIL;

  protected function getMethodName(): string {
    return self::METHOD;
  }
}
