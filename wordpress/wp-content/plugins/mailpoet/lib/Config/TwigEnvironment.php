<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Twig\Environment;

class TwigEnvironment extends Environment {


  private $templateClassPrefix = '__TwigTemplate_';

  /**
   * The original Environment of twig generates the class depending on PHP_VERSION.
   * We need to produce the same class regardless of PHP_VERSION. Therefore, we
   * overwrite this method.
   **/
  public function getTemplateClass(string $name, int $index = null): string {
    return $this->templateClassPrefix . \hash('sha256', $name) . (null === $index ? '' : '___' . $index);
  }
}
