<?php
namespace MailPoetVendor\Symfony\Component\Validator\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequence;
use MailPoetVendor\Symfony\Component\Validator\ConstraintViolationListInterface;
use MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
interface ValidatorInterface extends MetadataFactoryInterface
{
 public function validate($value, $constraints = null, $groups = null);
 public function validateProperty(object $object, string $propertyName, $groups = null);
 public function validatePropertyValue($objectOrClass, string $propertyName, $value, $groups = null);
 public function startContext();
 public function inContext(ExecutionContextInterface $context);
}
