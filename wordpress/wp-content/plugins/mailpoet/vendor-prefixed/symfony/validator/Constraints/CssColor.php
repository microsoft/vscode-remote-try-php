<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\InvalidArgumentException;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class CssColor extends Constraint
{
 public const HEX_LONG = 'hex_long';
 public const HEX_LONG_WITH_ALPHA = 'hex_long_with_alpha';
 public const HEX_SHORT = 'hex_short';
 public const HEX_SHORT_WITH_ALPHA = 'hex_short_with_alpha';
 public const BASIC_NAMED_COLORS = 'basic_named_colors';
 public const EXTENDED_NAMED_COLORS = 'extended_named_colors';
 public const SYSTEM_COLORS = 'system_colors';
 public const KEYWORDS = 'keywords';
 public const RGB = 'rgb';
 public const RGBA = 'rgba';
 public const HSL = 'hsl';
 public const HSLA = 'hsla';
 public const INVALID_FORMAT_ERROR = '454ab47b-aacf-4059-8f26-184b2dc9d48d';
 protected static $errorNames = [self::INVALID_FORMAT_ERROR => 'INVALID_FORMAT_ERROR'];
 private static $validationModes = [self::HEX_LONG, self::HEX_LONG_WITH_ALPHA, self::HEX_SHORT, self::HEX_SHORT_WITH_ALPHA, self::BASIC_NAMED_COLORS, self::EXTENDED_NAMED_COLORS, self::SYSTEM_COLORS, self::KEYWORDS, self::RGB, self::RGBA, self::HSL, self::HSLA];
 public $message = 'This value is not a valid CSS color.';
 public $formats;
 public function __construct($formats = [], string $message = null, array $groups = null, $payload = null, array $options = null)
 {
 $validationModesAsString = \implode(', ', self::$validationModes);
 if (!$formats) {
 $options['value'] = self::$validationModes;
 } elseif (\is_array($formats) && \is_string(\key($formats))) {
 $options = \array_merge($formats, $options ?? []);
 } elseif (\is_array($formats)) {
 if ([] === \array_intersect(self::$validationModes, $formats)) {
 throw new InvalidArgumentException(\sprintf('The "formats" parameter value is not valid. It must contain one or more of the following values: "%s".', $validationModesAsString));
 }
 $options['value'] = $formats;
 } elseif (\is_string($formats)) {
 if (!\in_array($formats, self::$validationModes)) {
 throw new InvalidArgumentException(\sprintf('The "formats" parameter value is not valid. It must contain one or more of the following values: "%s".', $validationModesAsString));
 }
 $options['value'] = [$formats];
 } else {
 throw new InvalidArgumentException('The "formats" parameter type is not valid. It should be a string or an array.');
 }
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 }
 public function getDefaultOption() : string
 {
 return 'formats';
 }
 public function getRequiredOptions() : array
 {
 return ['formats'];
 }
}
