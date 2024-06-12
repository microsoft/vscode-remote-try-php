<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use Traversable;
interface Result extends Abstraction\Result
{
 public function fetchAllKeyValue() : array;
 public function fetchAllAssociativeIndexed() : array;
 public function iterateKeyValue() : Traversable;
 public function iterateAssociativeIndexed() : Traversable;
}
