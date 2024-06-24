<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\pQuery;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\pQuery\DomNode as pQueryDomNode;

class DomNode extends pQueryDomNode {
  public $childClass = DomNode::class;
  public $parserClass = Html5Parser::class;

  public function getInnerText() {
    return html_entity_decode($this->toString(true, true, 1), ENT_NOQUOTES, 'UTF-8');
  }

  public function getOuterText() {
    return html_entity_decode($this->toString(), ENT_NOQUOTES, 'UTF-8');
  }

  public function query($query = '*') {
    $select = $this->select($query);
    $result = new pQuery((array)$select);
    return $result;
  }
}
