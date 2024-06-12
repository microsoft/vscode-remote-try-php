<?php
namespace MailPoetVendor\Symfony\Component\Validator\Context;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintViolationListInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping;
use MailPoetVendor\Symfony\Component\Validator\Mapping\MetadataInterface;
use MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface;
use MailPoetVendor\Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
interface ExecutionContextInterface
{
 public function addViolation(string $message, array $params = []);
 public function buildViolation(string $message, array $parameters = []);
 public function getValidator();
 public function getObject();
 public function setNode($value, ?object $object, MetadataInterface $metadata = null, string $propertyPath);
 public function setGroup(?string $group);
 public function setConstraint(Constraint $constraint);
 public function markGroupAsValidated(string $cacheKey, string $groupHash);
 public function isGroupValidated(string $cacheKey, string $groupHash);
 public function markConstraintAsValidated(string $cacheKey, string $constraintHash);
 public function isConstraintValidated(string $cacheKey, string $constraintHash);
 public function markObjectAsInitialized(string $cacheKey);
 public function isObjectInitialized(string $cacheKey);
 public function getViolations();
 public function getRoot();
 public function getValue();
 public function getMetadata();
 public function getGroup();
 public function getClassName();
 public function getPropertyName();
 public function getPropertyPath(string $subPath = '');
}
