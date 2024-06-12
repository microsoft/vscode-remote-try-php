<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
class IdenticalToValidator extends AbstractComparisonValidator
{
 protected function compareValues($value1, $value2)
 {
 return $value1 === $value2;
 }
 protected function getErrorCode()
 {
 return IdenticalTo::NOT_IDENTICAL_ERROR;
 }
}
