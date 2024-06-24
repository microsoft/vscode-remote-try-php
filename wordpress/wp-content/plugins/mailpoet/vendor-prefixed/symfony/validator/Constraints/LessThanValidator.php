<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
class LessThanValidator extends AbstractComparisonValidator
{
 protected function compareValues($value1, $value2)
 {
 return null === $value2 || $value1 < $value2;
 }
 protected function getErrorCode()
 {
 return LessThan::TOO_HIGH_ERROR;
 }
}
