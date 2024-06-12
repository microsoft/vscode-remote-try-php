<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\Validator;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Symfony\Contracts\Translation\TranslatorTrait;

class Translator implements \MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface {

  use TranslatorTrait;

  public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null) {
    return $this->trans($id, ['%count%' => $number] + $parameters, $domain, $locale);
  }
}
