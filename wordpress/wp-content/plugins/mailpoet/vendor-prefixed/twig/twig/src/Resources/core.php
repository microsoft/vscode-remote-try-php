<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Extension\CoreExtension;
function twig_cycle($values, $position)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::cycle($values, $position);
}
function twig_random(Environment $env, $values = null, $max = null)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::random($env->getCharset(), $values, $max);
}
function twig_date_format_filter(Environment $env, $date, $format = null, $timezone = null)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return $env->getExtension(CoreExtension::class)->formatDate($date, $format, $timezone);
}
function twig_date_modify_filter(Environment $env, $date, $modifier)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return $env->getExtension(CoreExtension::class)->modifyDate($date, $modifier);
}
function twig_sprintf($format, ...$values)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::sprintf($format, ...$values);
}
function twig_date_converter(Environment $env, $date = null, $timezone = null)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return $env->getExtension(CoreExtension::class)->convertDate($date, $timezone);
}
function twig_replace_filter($str, $from)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::replace($str, $from);
}
function twig_round($value, $precision = 0, $method = 'common')
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::round($value, $precision, $method);
}
function twig_number_format_filter(Environment $env, $number, $decimal = null, $decimalPoint = null, $thousandSep = null)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return $env->getExtension(CoreExtension::class)->formatNumber($number, $decimal, $decimalPoint, $thousandSep);
}
function twig_urlencode_filter($url)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::urlencode($url);
}
function twig_array_merge(...$arrays)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::merge(...$arrays);
}
function twig_slice(Environment $env, $item, $start, $length = null, $preserveKeys = \false)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::slice($env->getCharset(), $item, $start, $length, $preserveKeys);
}
function twig_first(Environment $env, $item)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::first($env->getCharset(), $item);
}
function twig_last(Environment $env, $item)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::last($env->getCharset(), $item);
}
function twig_join_filter($value, $glue = '', $and = null)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::join($value, $glue, $and);
}
function twig_split_filter(Environment $env, $value, $delimiter, $limit = null)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::split($env->getCharset(), $value, $delimiter, $limit);
}
function twig_get_array_keys_filter($array)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::keys($array);
}
function twig_reverse_filter(Environment $env, $item, $preserveKeys = \false)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::reverse($env->getCharset(), $item, $preserveKeys);
}
function twig_sort_filter(Environment $env, $array, $arrow = null)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::sort($env, $array, $arrow);
}
function twig_matches(string $regexp, ?string $str)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::matches($regexp, $str);
}
function twig_trim_filter($string, $characterMask = null, $side = 'both')
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::trim($string, $characterMask, $side);
}
function twig_nl2br($string)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::nl2br($string);
}
function twig_spaceless($content)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::spaceless($content);
}
function twig_convert_encoding($string, $to, $from)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::convertEncoding($string, $to, $from);
}
function twig_length_filter(Environment $env, $thing)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::length($env->getCharset(), $thing);
}
function twig_upper_filter(Environment $env, $string)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::upper($env->getCharset(), $string);
}
function twig_lower_filter(Environment $env, $string)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::lower($env->getCharset(), $string);
}
function twig_striptags($string, $allowable_tags = null)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::striptags($string, $allowable_tags);
}
function twig_title_string_filter(Environment $env, $string)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::titleCase($env->getCharset(), $string);
}
function twig_capitalize_string_filter(Environment $env, $string)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::capitalize($env->getCharset(), $string);
}
function twig_test_empty($value)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::testEmpty($value);
}
function twig_test_iterable($value)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return \is_iterable($value);
}
function twig_include(Environment $env, $context, $template, $variables = [], $withContext = \true, $ignoreMissing = \false, $sandboxed = \false)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::include($env, $context, $template, $variables, $withContext, $ignoreMissing, $sandboxed);
}
function twig_source(Environment $env, $name, $ignoreMissing = \false)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::source($env, $name, $ignoreMissing);
}
function twig_constant($constant, $object = null)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::constant($constant, $object);
}
function twig_constant_is_defined($constant, $object = null)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::constantIsDefined($constant, $object);
}
function twig_array_batch($items, $size, $fill = null, $preserveKeys = \true)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::batch($items, $size, $fill, $preserveKeys);
}
function twig_array_column($array, $name, $index = null) : array
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::column($array, $name, $index);
}
function twig_array_filter(Environment $env, $array, $arrow)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::filter($env, $array, $arrow);
}
function twig_array_map(Environment $env, $array, $arrow)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::map($env, $array, $arrow);
}
function twig_array_reduce(Environment $env, $array, $arrow, $initial = null)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::reduce($env, $array, $arrow, $initial);
}
function twig_array_some(Environment $env, $array, $arrow)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::arraySome($env, $array, $arrow);
}
function twig_array_every(Environment $env, $array, $arrow)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::arrayEvery($env, $array, $arrow);
}
function twig_check_arrow_in_sandbox(Environment $env, $arrow, $thing, $type)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return CoreExtension::checkArrowInSandbox($env, $arrow, $thing, $type);
}
