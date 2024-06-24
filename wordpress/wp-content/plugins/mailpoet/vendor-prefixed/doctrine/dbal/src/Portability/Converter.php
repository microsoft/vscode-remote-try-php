<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Portability;
if (!defined('ABSPATH')) exit;
use function array_change_key_case;
use function array_map;
use function array_reduce;
use function is_string;
use function rtrim;
use const CASE_LOWER;
use const CASE_UPPER;
final class Converter
{
 public const CASE_LOWER = CASE_LOWER;
 public const CASE_UPPER = CASE_UPPER;
 private $convertNumeric;
 private $convertAssociative;
 private $convertOne;
 private $convertAllNumeric;
 private $convertAllAssociative;
 private $convertFirstColumn;
 public function __construct(bool $convertEmptyStringToNull, bool $rightTrimString, ?int $case)
 {
 $convertValue = $this->createConvertValue($convertEmptyStringToNull, $rightTrimString);
 $convertNumeric = $this->createConvertRow($convertValue, null);
 $convertAssociative = $this->createConvertRow($convertValue, $case);
 $this->convertNumeric = $this->createConvert($convertNumeric, [self::class, 'id']);
 $this->convertAssociative = $this->createConvert($convertAssociative, [self::class, 'id']);
 $this->convertOne = $this->createConvert($convertValue, [self::class, 'id']);
 $this->convertAllNumeric = $this->createConvertAll($convertNumeric, [self::class, 'id']);
 $this->convertAllAssociative = $this->createConvertAll($convertAssociative, [self::class, 'id']);
 $this->convertFirstColumn = $this->createConvertAll($convertValue, [self::class, 'id']);
 }
 public function convertNumeric($row)
 {
 return ($this->convertNumeric)($row);
 }
 public function convertAssociative($row)
 {
 return ($this->convertAssociative)($row);
 }
 public function convertOne($value)
 {
 return ($this->convertOne)($value);
 }
 public function convertAllNumeric(array $data) : array
 {
 return ($this->convertAllNumeric)($data);
 }
 public function convertAllAssociative(array $data) : array
 {
 return ($this->convertAllAssociative)($data);
 }
 public function convertFirstColumn(array $data) : array
 {
 return ($this->convertFirstColumn)($data);
 }
 private static function id($value)
 {
 return $value;
 }
 private static function convertEmptyStringToNull($value)
 {
 if ($value === '') {
 return null;
 }
 return $value;
 }
 private static function rightTrimString($value)
 {
 if (!is_string($value)) {
 return $value;
 }
 return rtrim($value);
 }
 private function createConvertValue(bool $convertEmptyStringToNull, bool $rightTrimString) : ?callable
 {
 $functions = [];
 if ($convertEmptyStringToNull) {
 $functions[] = [self::class, 'convertEmptyStringToNull'];
 }
 if ($rightTrimString) {
 $functions[] = [self::class, 'rightTrimString'];
 }
 return $this->compose(...$functions);
 }
 private function createConvertRow(?callable $function, ?int $case) : ?callable
 {
 $functions = [];
 if ($function !== null) {
 $functions[] = $this->createMapper($function);
 }
 if ($case !== null) {
 $functions[] = static function (array $row) use($case) : array {
 return array_change_key_case($row, $case);
 };
 }
 return $this->compose(...$functions);
 }
 private function createConvert(?callable $function, callable $id) : callable
 {
 if ($function === null) {
 return $id;
 }
 return static function ($value) use($function) {
 if ($value === \false) {
 return \false;
 }
 return $function($value);
 };
 }
 private function createConvertAll(?callable $function, callable $id) : callable
 {
 if ($function === null) {
 return $id;
 }
 return $this->createMapper($function);
 }
 private function createMapper(callable $function) : callable
 {
 return static function (array $array) use($function) : array {
 return array_map($function, $array);
 };
 }
 private function compose(callable ...$functions) : ?callable
 {
 return array_reduce($functions, static function (?callable $carry, callable $item) : callable {
 if ($carry === null) {
 return $item;
 }
 return static function ($value) use($carry, $item) {
 return $item($carry($value));
 };
 });
 }
}
