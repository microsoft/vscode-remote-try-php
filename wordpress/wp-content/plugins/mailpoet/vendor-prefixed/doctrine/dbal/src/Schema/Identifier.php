<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
class Identifier extends AbstractAsset
{
 public function __construct($identifier, $quote = \false)
 {
 $this->_setName($identifier);
 if (!$quote || $this->_quoted) {
 return;
 }
 $this->_setName('"' . $this->getName() . '"');
 }
}
