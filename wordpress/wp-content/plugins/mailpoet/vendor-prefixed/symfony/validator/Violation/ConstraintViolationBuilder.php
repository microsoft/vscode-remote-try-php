<?php
namespace MailPoetVendor\Symfony\Component\Validator\Violation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintViolation;
use MailPoetVendor\Symfony\Component\Validator\ConstraintViolationList;
use MailPoetVendor\Symfony\Component\Validator\Util\PropertyPath;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface;
class ConstraintViolationBuilder implements ConstraintViolationBuilderInterface
{
 private $violations;
 private $message;
 private $parameters;
 private $root;
 private $invalidValue;
 private $propertyPath;
 private $translator;
 private $translationDomain;
 private $plural;
 private $constraint;
 private $code;
 private $cause;
 public function __construct(ConstraintViolationList $violations, ?Constraint $constraint, $message, array $parameters, $root, $propertyPath, $invalidValue, TranslatorInterface $translator, $translationDomain = null)
 {
 $this->violations = $violations;
 $this->message = $message;
 $this->parameters = $parameters;
 $this->root = $root;
 $this->propertyPath = $propertyPath;
 $this->invalidValue = $invalidValue;
 $this->translator = $translator;
 $this->translationDomain = $translationDomain;
 $this->constraint = $constraint;
 }
 public function atPath(string $path)
 {
 $this->propertyPath = PropertyPath::append($this->propertyPath, $path);
 return $this;
 }
 public function setParameter(string $key, string $value)
 {
 $this->parameters[$key] = $value;
 return $this;
 }
 public function setParameters(array $parameters)
 {
 $this->parameters = $parameters;
 return $this;
 }
 public function setTranslationDomain(string $translationDomain)
 {
 $this->translationDomain = $translationDomain;
 return $this;
 }
 public function setInvalidValue($invalidValue)
 {
 $this->invalidValue = $invalidValue;
 return $this;
 }
 public function setPlural(int $number)
 {
 $this->plural = $number;
 return $this;
 }
 public function setCode(?string $code)
 {
 $this->code = $code;
 return $this;
 }
 public function setCause($cause)
 {
 $this->cause = $cause;
 return $this;
 }
 public function addViolation()
 {
 if (null === $this->plural) {
 $translatedMessage = $this->translator->trans($this->message, $this->parameters, $this->translationDomain);
 } else {
 $translatedMessage = $this->translator->trans($this->message, ['%count%' => $this->plural] + $this->parameters, $this->translationDomain);
 }
 $this->violations->add(new ConstraintViolation($translatedMessage, $this->message, $this->parameters, $this->root, $this->propertyPath, $this->invalidValue, $this->plural, $this->code, $this->constraint, $this->cause));
 }
}
