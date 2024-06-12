<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use IteratorAggregate;
use ReturnTypeWillChange;
class StatementIterator implements IteratorAggregate
{
 private $statement;
 public function __construct(ResultStatement $statement)
 {
 $this->statement = $statement;
 }
 #[\ReturnTypeWillChange]
 public function getIterator()
 {
 while (($result = $this->statement->fetch()) !== \false) {
 (yield $result);
 }
 }
}
