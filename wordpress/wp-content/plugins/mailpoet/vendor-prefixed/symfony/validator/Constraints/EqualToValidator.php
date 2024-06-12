<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
class EqualToValidator extends AbstractComparisonValidator
{
 protected function compareValues($value1, $value2)
 {
 return $value1 == $value2;
 }
 protected function getErrorCode()
 {
 return EqualTo::NOT_EQUAL_ERROR;
 }
}
