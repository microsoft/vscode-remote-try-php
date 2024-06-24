<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Argument;
if (!defined('ABSPATH')) exit;
final class BoundArgument implements ArgumentInterface
{
 public const SERVICE_BINDING = 0;
 public const DEFAULTS_BINDING = 1;
 public const INSTANCEOF_BINDING = 2;
 private static $sequence = 0;
 private $value;
 private $identifier;
 private $used;
 private $type;
 private $file;
 public function __construct($value, bool $trackUsage = \true, int $type = 0, ?string $file = null)
 {
 $this->value = $value;
 if ($trackUsage) {
 $this->identifier = ++self::$sequence;
 } else {
 $this->used = \true;
 }
 $this->type = $type;
 $this->file = $file;
 }
 public function getValues() : array
 {
 return [$this->value, $this->identifier, $this->used, $this->type, $this->file];
 }
 public function setValues(array $values)
 {
 if (5 === \count($values)) {
 [$this->value, $this->identifier, $this->used, $this->type, $this->file] = $values;
 } else {
 [$this->value, $this->identifier, $this->used] = $values;
 }
 }
}
