<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
final class Query
{
 private string $sql;
 private array $params;
 private array $types;
 public function __construct(string $sql, array $params, array $types)
 {
 $this->sql = $sql;
 $this->params = $params;
 $this->types = $types;
 }
 public function getSQL() : string
 {
 return $this->sql;
 }
 public function getParams() : array
 {
 return $this->params;
 }
 public function getTypes() : array
 {
 return $this->types;
 }
}
