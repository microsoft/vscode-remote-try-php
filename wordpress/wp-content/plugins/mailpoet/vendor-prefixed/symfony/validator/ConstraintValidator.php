<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextInterface;
abstract class ConstraintValidator implements ConstraintValidatorInterface
{
 public const PRETTY_DATE = 1;
 public const OBJECT_TO_STRING = 2;
 protected $context;
 public function initialize(ExecutionContextInterface $context)
 {
 $this->context = $context;
 }
 protected function formatTypeOf($value)
 {
 return \get_debug_type($value);
 }
 protected function formatValue($value, int $format = 0)
 {
 if ($format & self::PRETTY_DATE && $value instanceof \DateTimeInterface) {
 if (\class_exists(\IntlDateFormatter::class)) {
 $formatter = new \IntlDateFormatter(\Locale::getDefault(), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT, 'UTC');
 return $formatter->format(new \DateTime($value->format('Y-m-d H:i:s.u'), new \DateTimeZone('UTC')));
 }
 return $value->format('Y-m-d H:i:s');
 }
 if (\is_object($value)) {
 if ($format & self::OBJECT_TO_STRING && \method_exists($value, '__toString')) {
 return $value->__toString();
 }
 return 'object';
 }
 if (\is_array($value)) {
 return 'array';
 }
 if (\is_string($value)) {
 return '"' . $value . '"';
 }
 if (\is_resource($value)) {
 return 'resource';
 }
 if (null === $value) {
 return 'null';
 }
 if (\false === $value) {
 return 'false';
 }
 if (\true === $value) {
 return 'true';
 }
 return (string) $value;
 }
 protected function formatValues(array $values, int $format = 0)
 {
 foreach ($values as $key => $value) {
 $values[$key] = $this->formatValue($value, $format);
 }
 return \implode(', ', $values);
 }
}
