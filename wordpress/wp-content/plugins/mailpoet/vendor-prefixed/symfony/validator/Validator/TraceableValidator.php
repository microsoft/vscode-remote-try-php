<?php
namespace MailPoetVendor\Symfony\Component\Validator\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextInterface;
use MailPoetVendor\Symfony\Contracts\Service\ResetInterface;
class TraceableValidator implements ValidatorInterface, ResetInterface
{
 private $validator;
 private $collectedData = [];
 public function __construct(ValidatorInterface $validator)
 {
 $this->validator = $validator;
 }
 public function getCollectedData()
 {
 return $this->collectedData;
 }
 public function reset()
 {
 $this->collectedData = [];
 }
 public function getMetadataFor($value)
 {
 return $this->validator->getMetadataFor($value);
 }
 public function hasMetadataFor($value)
 {
 return $this->validator->hasMetadataFor($value);
 }
 public function validate($value, $constraints = null, $groups = null)
 {
 $violations = $this->validator->validate($value, $constraints, $groups);
 $trace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 7);
 $file = $trace[0]['file'];
 $line = $trace[0]['line'];
 for ($i = 1; $i < 7; ++$i) {
 if (isset($trace[$i]['class'], $trace[$i]['function']) && 'validate' === $trace[$i]['function'] && \is_a($trace[$i]['class'], ValidatorInterface::class, \true)) {
 $file = $trace[$i]['file'];
 $line = $trace[$i]['line'];
 while (++$i < 7) {
 if (isset($trace[$i]['function'], $trace[$i]['file']) && empty($trace[$i]['class']) && !\str_starts_with($trace[$i]['function'], 'call_user_func')) {
 $file = $trace[$i]['file'];
 $line = $trace[$i]['line'];
 break;
 }
 }
 break;
 }
 }
 $name = \str_replace('\\', '/', $file);
 $name = \substr($name, \strrpos($name, '/') + 1);
 $this->collectedData[] = ['caller' => \compact('name', 'file', 'line'), 'context' => \compact('value', 'constraints', 'groups'), 'violations' => \iterator_to_array($violations)];
 return $violations;
 }
 public function validateProperty(object $object, string $propertyName, $groups = null)
 {
 return $this->validator->validateProperty($object, $propertyName, $groups);
 }
 public function validatePropertyValue($objectOrClass, string $propertyName, $value, $groups = null)
 {
 return $this->validator->validatePropertyValue($objectOrClass, $propertyName, $value, $groups);
 }
 public function startContext()
 {
 return $this->validator->startContext();
 }
 public function inContext(ExecutionContextInterface $context)
 {
 return $this->validator->inContext($context);
 }
}
