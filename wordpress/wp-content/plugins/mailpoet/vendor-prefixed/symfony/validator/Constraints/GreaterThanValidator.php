<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
class GreaterThanValidator extends AbstractComparisonValidator
{
 protected function compareValues($value1, $value2)
 {
 return null === $value2 || $value1 > $value2;
 }
 protected function getErrorCode()
 {
 return GreaterThan::TOO_LOW_ERROR;
 }
}
