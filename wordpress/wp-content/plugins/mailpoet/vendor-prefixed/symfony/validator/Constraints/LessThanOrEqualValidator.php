<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
class LessThanOrEqualValidator extends AbstractComparisonValidator
{
 protected function compareValues($value1, $value2)
 {
 return null === $value2 || $value1 <= $value2;
 }
 protected function getErrorCode()
 {
 return LessThanOrEqual::TOO_HIGH_ERROR;
 }
}
