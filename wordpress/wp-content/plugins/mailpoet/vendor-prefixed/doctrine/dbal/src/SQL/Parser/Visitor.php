<?php
namespace MailPoetVendor\Doctrine\DBAL\SQL\Parser;
if (!defined('ABSPATH')) exit;
interface Visitor
{
 public function acceptPositionalParameter(string $sql) : void;
 public function acceptNamedParameter(string $sql) : void;
 public function acceptOther(string $sql) : void;
}
