<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
class View extends AbstractAsset
{
 private $sql;
 public function __construct($name, $sql)
 {
 $this->_setName($name);
 $this->sql = $sql;
 }
 public function getSql()
 {
 return $this->sql;
 }
}
