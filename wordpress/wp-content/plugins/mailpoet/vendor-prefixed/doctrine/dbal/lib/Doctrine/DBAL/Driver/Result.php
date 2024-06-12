<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
interface Result
{
 public function fetchNumeric();
 public function fetchAssociative();
 public function fetchOne();
 public function fetchAllNumeric() : array;
 public function fetchAllAssociative() : array;
 public function fetchFirstColumn() : array;
 public function rowCount();
 public function columnCount();
 public function free() : void;
}
