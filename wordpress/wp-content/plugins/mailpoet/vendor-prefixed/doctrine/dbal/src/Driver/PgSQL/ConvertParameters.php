<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\PgSQL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\SQL\Parser\Visitor;
use function count;
use function implode;
final class ConvertParameters implements Visitor
{
 private array $buffer = [];
 private array $parameterMap = [];
 public function acceptPositionalParameter(string $sql) : void
 {
 $position = count($this->parameterMap) + 1;
 $this->parameterMap[$position] = $position;
 $this->buffer[] = '$' . $position;
 }
 public function acceptNamedParameter(string $sql) : void
 {
 $position = count($this->parameterMap) + 1;
 $this->parameterMap[$sql] = $position;
 $this->buffer[] = '$' . $position;
 }
 public function acceptOther(string $sql) : void
 {
 $this->buffer[] = $sql;
 }
 public function getSQL() : string
 {
 return implode('', $this->buffer);
 }
 public function getParameterMap() : array
 {
 return $this->parameterMap;
 }
}
