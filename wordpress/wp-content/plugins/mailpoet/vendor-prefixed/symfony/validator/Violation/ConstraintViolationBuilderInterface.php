<?php
namespace MailPoetVendor\Symfony\Component\Validator\Violation;
if (!defined('ABSPATH')) exit;
interface ConstraintViolationBuilderInterface
{
 public function atPath(string $path);
 public function setParameter(string $key, string $value);
 public function setParameters(array $parameters);
 public function setTranslationDomain(string $translationDomain);
 public function setInvalidValue($invalidValue);
 public function setPlural(int $number);
 public function setCode(?string $code);
 public function setCause($cause);
 public function addViolation();
}
