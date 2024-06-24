<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Twig;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Localizer;
use MailPoet\InvalidStateException;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Twig\Extension\AbstractExtension;
use MailPoetVendor\Twig\TwigFunction;

class I18n extends AbstractExtension {

  private $textDomains;

  public function __construct(
    $textDomain
  ) {
    // set text domain
    $this->textDomains = [$textDomain, 'woocommerce'];
  }

  public function getFunctions() {
    // twig custom functions
    $twigFunctions = [];
    // list of WP functions to map
    $functions = [
      'localize' => 'localize',
      '__' => 'translate',
      'esc_html__' => 'translateEscHTML',
      'esc_attr__' => 'translateEscAttr',
      '_n' => 'pluralize',
      '_x' => 'translateWithContext',
      'get_locale' => 'getLocale',
      'date' => 'date',
    ];

    foreach ($functions as $twigFunction => $function) {
      $callable = [$this, $function];
      if (!is_callable($callable)) {
        throw new InvalidStateException('Trying to register non-existing function to Twig.');
      }
      $twigFunctions[] = new TwigFunction(
        $twigFunction,
        $callable,
        ['is_safe' => ['all']]
      );
    }
    return $twigFunctions;
  }

  public function localize() {
    $args = func_get_args();
    /** @var array $translations */
    $translations = array_shift($args);
    $output = [];
    foreach ($translations as $key => $translation) {
      $output[] =
        'MailPoet.I18n.add("' . $key . '", "' . str_replace(['"', "\n", "\r"], ['\"', " ", ""], $translation ?? '') . '");';
    }
    WPFunctions::get()->wpAddInlineScript('mailpoet_mailpoet', join("\n", $output));
  }

  public function translate() {
    $args = func_get_args();

    return call_user_func_array('__', $this->setTextDomain($args));
  }

  public function translateEscHTML() {
    $args = func_get_args();

    return call_user_func_array('esc_html__', $this->setTextDomain($args));
  }

  public function translateEscAttr() {
    $args = func_get_args();

    return call_user_func_array('esc_attr__', $this->setTextDomain($args));
  }

  public function pluralize() {
    $args = func_get_args();

    return call_user_func_array('_n', $this->setTextDomain($args));
  }

  public function translateWithContext() {
    $args = func_get_args();

    return call_user_func_array('_x', $this->setTextDomain($args));
  }

  public function getLocale() {
    $localizer = new Localizer;
    return $localizer->locale();
  }

  public function date() {
    $args = func_get_args();
    /** @var int|null $date */
    $date = (isset($args[0])) ? $args[0] : null;
    $dateFormat = (isset($args[1])) ? $args[1] : WPFunctions::get()->getOption('date_format');

    if (empty($date)) return;

    // check if it's an int passed as a string
    if ((string)(int)$date === $date) {
      $date = (int)$date;
    } else if (!is_int($date)) {
      $date = strtotime($date);
    }

    return WPFunctions::get()->getDateFromGmt(date('Y-m-d H:i:s', (int)$date), $dateFormat);
  }

  private function setTextDomain($args = []) {
    // make sure that the last argument is our text domain
    if (!in_array($args[count($args) - 1], $this->textDomains)) {
      // otherwise add it to the list of arguments
      $args[] = $this->textDomains[0];
    }
    return $args;
  }
}
