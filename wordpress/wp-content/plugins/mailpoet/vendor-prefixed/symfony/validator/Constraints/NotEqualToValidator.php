<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
class NotEqualToValidator extends AbstractComparisonValidator
{
 protected function compareValues($value1, $value2)
 {
 return $value1 != $value2;
 }
 protected function getErrorCode()
 {
 return NotEqualTo::IS_EQUAL_ERROR;
 }
}
