<?php
namespace MailPoetVendor\Symfony\Component\Translation;
if (!defined('ABSPATH')) exit;
@\trigger_error(\sprintf('The "%s" class is deprecated since Symfony 4.2, use IdentityTranslator instead.', MessageSelector::class), \E_USER_DEPRECATED);
use MailPoetVendor\Symfony\Component\Translation\Exception\InvalidArgumentException;
class MessageSelector
{
 public function choose($message, $number, $locale)
 {
 $parts = [];
 if (\preg_match('/^\\|++$/', $message)) {
 $parts = \explode('|', $message);
 } elseif (\preg_match_all('/(?:\\|\\||[^\\|])++/', $message, $matches)) {
 $parts = $matches[0];
 }
 $explicitRules = [];
 $standardRules = [];
 foreach ($parts as $part) {
 $part = \trim(\str_replace('||', '|', $part));
 if (\preg_match('/^(?P<interval>' . Interval::getIntervalRegexp() . ')\\s*(?P<message>.*?)$/xs', $part, $matches)) {
 $explicitRules[$matches['interval']] = $matches['message'];
 } elseif (\preg_match('/^\\w+\\:\\s*(.*?)$/', $part, $matches)) {
 $standardRules[] = $matches[1];
 } else {
 $standardRules[] = $part;
 }
 }
 // try to match an explicit rule, then fallback to the standard ones
 foreach ($explicitRules as $interval => $m) {
 if (Interval::test($number, $interval)) {
 return $m;
 }
 }
 $position = PluralizationRules::get($number, $locale);
 if (!isset($standardRules[$position])) {
 // when there's exactly one rule given, and that rule is a standard
 // rule, use this rule
 if (1 === \count($parts) && isset($standardRules[0])) {
 return $standardRules[0];
 }
 throw new InvalidArgumentException(\sprintf('Unable to choose a translation for "%s" with locale "%s" for value "%d". Double check that this translation has the correct plural options (e.g. "There is one apple|There are %%count%% apples").', $message, $locale, $number));
 }
 return $standardRules[$position];
 }
}
