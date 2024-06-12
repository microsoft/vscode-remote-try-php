<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
class GreaterThanOrEqualValidator extends AbstractComparisonValidator
{
 protected function compareValues($value1, $value2)
 {
 return null === $value2 || $value1 >= $value2;
 }
 protected function getErrorCode()
 {
 return GreaterThanOrEqual::TOO_LOW_ERROR;
 }
}
