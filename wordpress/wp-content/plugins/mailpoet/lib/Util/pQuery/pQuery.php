<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\pQuery;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\pQuery\pQuery as pQuerypQuery;

// extend pQuery class to use UTF-8 encoding when getting elements' inner/outer text
// phpcs:ignore Squiz.Classes.ValidClassName
class pQuery extends pQuerypQuery {
  public static function parseStr($html): DomNode {
    $parser = new Html5Parser($html);

    if (!$parser->root instanceof DomNode) {
      // this condition shouldn't happen it is here only for PHPStan
      throw new \Exception('Renderer is not configured correctly');
    }

    return $parser->root;
  }
}
