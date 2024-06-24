<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
use MailPoetVendor\Twig\ExpressionParser;
use MailPoetVendor\Twig\Markup;
use MailPoetVendor\Twig\Node\Expression\Binary\AddBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\AndBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\BitwiseAndBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\BitwiseOrBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\BitwiseXorBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\ConcatBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\DivBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\EndsWithBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\EqualBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\FloorDivBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\GreaterBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\GreaterEqualBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\HasEveryBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\HasSomeBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\InBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\LessBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\LessEqualBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\MatchesBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\ModBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\MulBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\NotEqualBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\NotInBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\OrBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\PowerBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\RangeBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\SpaceshipBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\StartsWithBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\SubBinary;
use MailPoetVendor\Twig\Node\Expression\Filter\DefaultFilter;
use MailPoetVendor\Twig\Node\Expression\NullCoalesceExpression;
use MailPoetVendor\Twig\Node\Expression\Test\ConstantTest;
use MailPoetVendor\Twig\Node\Expression\Test\DefinedTest;
use MailPoetVendor\Twig\Node\Expression\Test\DivisiblebyTest;
use MailPoetVendor\Twig\Node\Expression\Test\EvenTest;
use MailPoetVendor\Twig\Node\Expression\Test\NullTest;
use MailPoetVendor\Twig\Node\Expression\Test\OddTest;
use MailPoetVendor\Twig\Node\Expression\Test\SameasTest;
use MailPoetVendor\Twig\Node\Expression\Unary\NegUnary;
use MailPoetVendor\Twig\Node\Expression\Unary\NotUnary;
use MailPoetVendor\Twig\Node\Expression\Unary\PosUnary;
use MailPoetVendor\Twig\NodeVisitor\MacroAutoImportNodeVisitor;
use MailPoetVendor\Twig\Source;
use MailPoetVendor\Twig\Template;
use MailPoetVendor\Twig\TemplateWrapper;
use MailPoetVendor\Twig\TokenParser\ApplyTokenParser;
use MailPoetVendor\Twig\TokenParser\BlockTokenParser;
use MailPoetVendor\Twig\TokenParser\DeprecatedTokenParser;
use MailPoetVendor\Twig\TokenParser\DoTokenParser;
use MailPoetVendor\Twig\TokenParser\EmbedTokenParser;
use MailPoetVendor\Twig\TokenParser\ExtendsTokenParser;
use MailPoetVendor\Twig\TokenParser\FlushTokenParser;
use MailPoetVendor\Twig\TokenParser\ForTokenParser;
use MailPoetVendor\Twig\TokenParser\FromTokenParser;
use MailPoetVendor\Twig\TokenParser\IfTokenParser;
use MailPoetVendor\Twig\TokenParser\ImportTokenParser;
use MailPoetVendor\Twig\TokenParser\IncludeTokenParser;
use MailPoetVendor\Twig\TokenParser\MacroTokenParser;
use MailPoetVendor\Twig\TokenParser\SetTokenParser;
use MailPoetVendor\Twig\TokenParser\UseTokenParser;
use MailPoetVendor\Twig\TokenParser\WithTokenParser;
use MailPoetVendor\Twig\TwigFilter;
use MailPoetVendor\Twig\TwigFunction;
use MailPoetVendor\Twig\TwigTest;
final class CoreExtension extends AbstractExtension
{
 private $dateFormats = ['F j, Y H:i', '%d days'];
 private $numberFormat = [0, '.', ','];
 private $timezone = null;
 public function setDateFormat($format = null, $dateIntervalFormat = null)
 {
 if (null !== $format) {
 $this->dateFormats[0] = $format;
 }
 if (null !== $dateIntervalFormat) {
 $this->dateFormats[1] = $dateIntervalFormat;
 }
 }
 public function getDateFormat()
 {
 return $this->dateFormats;
 }
 public function setTimezone($timezone)
 {
 $this->timezone = $timezone instanceof \DateTimeZone ? $timezone : new \DateTimeZone($timezone);
 }
 public function getTimezone()
 {
 if (null === $this->timezone) {
 $this->timezone = new \DateTimeZone(\date_default_timezone_get());
 }
 return $this->timezone;
 }
 public function setNumberFormat($decimal, $decimalPoint, $thousandSep)
 {
 $this->numberFormat = [$decimal, $decimalPoint, $thousandSep];
 }
 public function getNumberFormat()
 {
 return $this->numberFormat;
 }
 public function getTokenParsers() : array
 {
 return [new ApplyTokenParser(), new ForTokenParser(), new IfTokenParser(), new ExtendsTokenParser(), new IncludeTokenParser(), new BlockTokenParser(), new UseTokenParser(), new MacroTokenParser(), new ImportTokenParser(), new FromTokenParser(), new SetTokenParser(), new FlushTokenParser(), new DoTokenParser(), new EmbedTokenParser(), new WithTokenParser(), new DeprecatedTokenParser()];
 }
 public function getFilters() : array
 {
 return [
 // formatting filters
 new TwigFilter('date', [$this, 'formatDate']),
 new TwigFilter('date_modify', [$this, 'modifyDate']),
 new TwigFilter('format', [self::class, 'sprintf']),
 new TwigFilter('replace', [self::class, 'replace']),
 new TwigFilter('number_format', [$this, 'formatNumber']),
 new TwigFilter('abs', 'abs'),
 new TwigFilter('round', [self::class, 'round']),
 // encoding
 new TwigFilter('url_encode', [self::class, 'urlencode']),
 new TwigFilter('json_encode', 'json_encode'),
 new TwigFilter('convert_encoding', [self::class, 'convertEncoding']),
 // string filters
 new TwigFilter('title', [self::class, 'titleCase'], ['needs_charset' => \true]),
 new TwigFilter('capitalize', [self::class, 'capitalize'], ['needs_charset' => \true]),
 new TwigFilter('upper', [self::class, 'upper'], ['needs_charset' => \true]),
 new TwigFilter('lower', [self::class, 'lower'], ['needs_charset' => \true]),
 new TwigFilter('striptags', [self::class, 'striptags']),
 new TwigFilter('trim', [self::class, 'trim']),
 new TwigFilter('nl2br', [self::class, 'nl2br'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
 new TwigFilter('spaceless', [self::class, 'spaceless'], ['is_safe' => ['html']]),
 // array helpers
 new TwigFilter('join', [self::class, 'join']),
 new TwigFilter('split', [self::class, 'split'], ['needs_charset' => \true]),
 new TwigFilter('sort', [self::class, 'sort'], ['needs_environment' => \true]),
 new TwigFilter('merge', [self::class, 'merge']),
 new TwigFilter('batch', [self::class, 'batch']),
 new TwigFilter('column', [self::class, 'column']),
 new TwigFilter('filter', [self::class, 'filter'], ['needs_environment' => \true]),
 new TwigFilter('map', [self::class, 'map'], ['needs_environment' => \true]),
 new TwigFilter('reduce', [self::class, 'reduce'], ['needs_environment' => \true]),
 // string/array filters
 new TwigFilter('reverse', [self::class, 'reverse'], ['needs_charset' => \true]),
 new TwigFilter('length', [self::class, 'length'], ['needs_charset' => \true]),
 new TwigFilter('slice', [self::class, 'slice'], ['needs_charset' => \true]),
 new TwigFilter('first', [self::class, 'first'], ['needs_charset' => \true]),
 new TwigFilter('last', [self::class, 'last'], ['needs_charset' => \true]),
 // iteration and runtime
 new TwigFilter('default', [self::class, 'default'], ['node_class' => DefaultFilter::class]),
 new TwigFilter('keys', [self::class, 'keys']),
 ];
 }
 public function getFunctions() : array
 {
 return [new TwigFunction('max', 'max'), new TwigFunction('min', 'min'), new TwigFunction('range', 'range'), new TwigFunction('constant', [self::class, 'constant']), new TwigFunction('cycle', [self::class, 'cycle']), new TwigFunction('random', [self::class, 'random'], ['needs_charset' => \true]), new TwigFunction('date', [$this, 'convertDate']), new TwigFunction('include', [self::class, 'include'], ['needs_environment' => \true, 'needs_context' => \true, 'is_safe' => ['all']]), new TwigFunction('source', [self::class, 'source'], ['needs_environment' => \true, 'is_safe' => ['all']])];
 }
 public function getTests() : array
 {
 return [new TwigTest('even', null, ['node_class' => EvenTest::class]), new TwigTest('odd', null, ['node_class' => OddTest::class]), new TwigTest('defined', null, ['node_class' => DefinedTest::class]), new TwigTest('same as', null, ['node_class' => SameasTest::class, 'one_mandatory_argument' => \true]), new TwigTest('none', null, ['node_class' => NullTest::class]), new TwigTest('null', null, ['node_class' => NullTest::class]), new TwigTest('divisible by', null, ['node_class' => DivisiblebyTest::class, 'one_mandatory_argument' => \true]), new TwigTest('constant', null, ['node_class' => ConstantTest::class]), new TwigTest('empty', [self::class, 'testEmpty']), new TwigTest('iterable', 'is_iterable')];
 }
 public function getNodeVisitors() : array
 {
 return [new MacroAutoImportNodeVisitor()];
 }
 public function getOperators() : array
 {
 return [['not' => ['precedence' => 50, 'class' => NotUnary::class], '-' => ['precedence' => 500, 'class' => NegUnary::class], '+' => ['precedence' => 500, 'class' => PosUnary::class]], ['or' => ['precedence' => 10, 'class' => OrBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'and' => ['precedence' => 15, 'class' => AndBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'b-or' => ['precedence' => 16, 'class' => BitwiseOrBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'b-xor' => ['precedence' => 17, 'class' => BitwiseXorBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'b-and' => ['precedence' => 18, 'class' => BitwiseAndBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '==' => ['precedence' => 20, 'class' => EqualBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '!=' => ['precedence' => 20, 'class' => NotEqualBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '<=>' => ['precedence' => 20, 'class' => SpaceshipBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '<' => ['precedence' => 20, 'class' => LessBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '>' => ['precedence' => 20, 'class' => GreaterBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '>=' => ['precedence' => 20, 'class' => GreaterEqualBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '<=' => ['precedence' => 20, 'class' => LessEqualBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'not in' => ['precedence' => 20, 'class' => NotInBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'in' => ['precedence' => 20, 'class' => InBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'matches' => ['precedence' => 20, 'class' => MatchesBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'starts with' => ['precedence' => 20, 'class' => StartsWithBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'ends with' => ['precedence' => 20, 'class' => EndsWithBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'has some' => ['precedence' => 20, 'class' => HasSomeBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'has every' => ['precedence' => 20, 'class' => HasEveryBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '..' => ['precedence' => 25, 'class' => RangeBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '+' => ['precedence' => 30, 'class' => AddBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '-' => ['precedence' => 30, 'class' => SubBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '~' => ['precedence' => 40, 'class' => ConcatBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '*' => ['precedence' => 60, 'class' => MulBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '/' => ['precedence' => 60, 'class' => DivBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '//' => ['precedence' => 60, 'class' => FloorDivBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], '%' => ['precedence' => 60, 'class' => ModBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'is' => ['precedence' => 100, 'associativity' => ExpressionParser::OPERATOR_LEFT], 'is not' => ['precedence' => 100, 'associativity' => ExpressionParser::OPERATOR_LEFT], '**' => ['precedence' => 200, 'class' => PowerBinary::class, 'associativity' => ExpressionParser::OPERATOR_RIGHT], '??' => ['precedence' => 300, 'class' => NullCoalesceExpression::class, 'associativity' => ExpressionParser::OPERATOR_RIGHT]]];
 }
 public static function cycle($values, $position) : string
 {
 if (!\is_array($values) && !$values instanceof \ArrayAccess) {
 return $values;
 }
 if (!\count($values)) {
 throw new RuntimeError('The "cycle" function does not work on empty arrays.');
 }
 return $values[$position % \count($values)];
 }
 public static function random(string $charset, $values = null, $max = null)
 {
 if (null === $values) {
 return null === $max ? \mt_rand() : \mt_rand(0, (int) $max);
 }
 if (\is_int($values) || \is_float($values)) {
 if (null === $max) {
 if ($values < 0) {
 $max = 0;
 $min = $values;
 } else {
 $max = $values;
 $min = 0;
 }
 } else {
 $min = $values;
 }
 return \mt_rand((int) $min, (int) $max);
 }
 if (\is_string($values)) {
 if ('' === $values) {
 return '';
 }
 if ('UTF-8' !== $charset) {
 $values = self::convertEncoding($values, 'UTF-8', $charset);
 }
 // unicode version of str_split()
 // split at all positions, but not after the start and not before the end
 $values = \preg_split('/(?<!^)(?!$)/u', $values);
 if ('UTF-8' !== $charset) {
 foreach ($values as $i => $value) {
 $values[$i] = self::convertEncoding($value, $charset, 'UTF-8');
 }
 }
 }
 if (!\is_iterable($values)) {
 return $values;
 }
 $values = self::toArray($values);
 if (0 === \count($values)) {
 throw new RuntimeError('The random function cannot pick from an empty array.');
 }
 return $values[\array_rand($values, 1)];
 }
 public function formatDate($date, $format = null, $timezone = null) : string
 {
 if (null === $format) {
 $formats = $this->getDateFormat();
 $format = $date instanceof \DateInterval ? $formats[1] : $formats[0];
 }
 if ($date instanceof \DateInterval) {
 return $date->format($format);
 }
 return $this->convertDate($date, $timezone)->format($format);
 }
 public function modifyDate($date, $modifier)
 {
 return $this->convertDate($date, \false)->modify($modifier);
 }
 public static function sprintf($format, ...$values) : string
 {
 return \sprintf($format ?? '', ...$values);
 }
 public static function dateConverter(Environment $env, $date, $format = null, $timezone = null) : string
 {
 return $env->getExtension(self::class)->formatDate($date, $format, $timezone);
 }
 public function convertDate($date = null, $timezone = null)
 {
 // determine the timezone
 if (\false !== $timezone) {
 if (null === $timezone) {
 $timezone = $this->getTimezone();
 } elseif (!$timezone instanceof \DateTimeZone) {
 $timezone = new \DateTimeZone($timezone);
 }
 }
 // immutable dates
 if ($date instanceof \DateTimeImmutable) {
 return \false !== $timezone ? $date->setTimezone($timezone) : $date;
 }
 if ($date instanceof \DateTime) {
 $date = clone $date;
 if (\false !== $timezone) {
 $date->setTimezone($timezone);
 }
 return $date;
 }
 if (null === $date || 'now' === $date) {
 if (null === $date) {
 $date = 'now';
 }
 return new \DateTime($date, \false !== $timezone ? $timezone : $this->getTimezone());
 }
 $asString = (string) $date;
 if (\ctype_digit($asString) || !empty($asString) && '-' === $asString[0] && \ctype_digit(\substr($asString, 1))) {
 $date = new \DateTime('@' . $date);
 } else {
 $date = new \DateTime($date, $this->getTimezone());
 }
 if (\false !== $timezone) {
 $date->setTimezone($timezone);
 }
 return $date;
 }
 public static function replace($str, $from) : string
 {
 if (!\is_iterable($from)) {
 throw new RuntimeError(\sprintf('The "replace" filter expects an array or "Traversable" as replace values, got "%s".', \is_object($from) ? \get_class($from) : \gettype($from)));
 }
 return \strtr($str ?? '', self::toArray($from));
 }
 public static function round($value, $precision = 0, $method = 'common')
 {
 $value = (float) $value;
 if ('common' === $method) {
 return \round($value, $precision);
 }
 if ('ceil' !== $method && 'floor' !== $method) {
 throw new RuntimeError('The round filter only supports the "common", "ceil", and "floor" methods.');
 }
 return $method($value * 10 ** $precision) / 10 ** $precision;
 }
 public function formatNumber($number, $decimal = null, $decimalPoint = null, $thousandSep = null) : string
 {
 $defaults = $this->getNumberFormat();
 if (null === $decimal) {
 $decimal = $defaults[0];
 }
 if (null === $decimalPoint) {
 $decimalPoint = $defaults[1];
 }
 if (null === $thousandSep) {
 $thousandSep = $defaults[2];
 }
 return \number_format((float) $number, $decimal, $decimalPoint, $thousandSep);
 }
 public static function urlencode($url) : string
 {
 if (\is_array($url)) {
 return \http_build_query($url, '', '&', \PHP_QUERY_RFC3986);
 }
 return \rawurlencode($url ?? '');
 }
 public static function merge(...$arrays) : array
 {
 $result = [];
 foreach ($arrays as $argNumber => $array) {
 if (!\is_iterable($array)) {
 throw new RuntimeError(\sprintf('The merge filter only works with arrays or "Traversable", got "%s" for argument %d.', \gettype($array), $argNumber + 1));
 }
 $result = \array_merge($result, self::toArray($array));
 }
 return $result;
 }
 public static function slice(string $charset, $item, $start, $length = null, $preserveKeys = \false)
 {
 if ($item instanceof \Traversable) {
 while ($item instanceof \IteratorAggregate) {
 $item = $item->getIterator();
 }
 if ($start >= 0 && $length >= 0 && $item instanceof \Iterator) {
 try {
 return \iterator_to_array(new \LimitIterator($item, $start, $length ?? -1), $preserveKeys);
 } catch (\OutOfBoundsException $e) {
 return [];
 }
 }
 $item = \iterator_to_array($item, $preserveKeys);
 }
 if (\is_array($item)) {
 return \array_slice($item, $start, $length, $preserveKeys);
 }
 return \mb_substr((string) $item, $start, $length, $charset);
 }
 public static function first(string $charset, $item)
 {
 $elements = self::slice($charset, $item, 0, 1, \false);
 return \is_string($elements) ? $elements : \current($elements);
 }
 public static function last(string $charset, $item)
 {
 $elements = self::slice($charset, $item, -1, 1, \false);
 return \is_string($elements) ? $elements : \current($elements);
 }
 public static function join($value, $glue = '', $and = null) : string
 {
 if (!\is_iterable($value)) {
 $value = (array) $value;
 }
 $value = self::toArray($value, \false);
 if (0 === \count($value)) {
 return '';
 }
 if (null === $and || $and === $glue) {
 return \implode($glue, $value);
 }
 if (1 === \count($value)) {
 return $value[0];
 }
 return \implode($glue, \array_slice($value, 0, -1)) . $and . $value[\count($value) - 1];
 }
 public static function split(string $charset, $value, $delimiter, $limit = null) : array
 {
 $value = $value ?? '';
 if ('' !== $delimiter) {
 return null === $limit ? \explode($delimiter, $value) : \explode($delimiter, $value, $limit);
 }
 if ($limit <= 1) {
 return \preg_split('/(?<!^)(?!$)/u', $value);
 }
 $length = \mb_strlen($value, $charset);
 if ($length < $limit) {
 return [$value];
 }
 $r = [];
 for ($i = 0; $i < $length; $i += $limit) {
 $r[] = \mb_substr($value, $i, $limit, $charset);
 }
 return $r;
 }
 // The '_default' filter is used internally to avoid using the ternary operator
 // which costs a lot for big contexts (before PHP 5.4). So, on average,
 // a function call is cheaper.
 public static function default($value, $default = '')
 {
 if (self::testEmpty($value)) {
 return $default;
 }
 return $value;
 }
 public static function keys($array) : array
 {
 if ($array instanceof \Traversable) {
 while ($array instanceof \IteratorAggregate) {
 $array = $array->getIterator();
 }
 $keys = [];
 if ($array instanceof \Iterator) {
 $array->rewind();
 while ($array->valid()) {
 $keys[] = $array->key();
 $array->next();
 }
 return $keys;
 }
 foreach ($array as $key => $item) {
 $keys[] = $key;
 }
 return $keys;
 }
 if (!\is_array($array)) {
 return [];
 }
 return \array_keys($array);
 }
 public static function reverse(string $charset, $item, $preserveKeys = \false)
 {
 if ($item instanceof \Traversable) {
 return \array_reverse(\iterator_to_array($item), $preserveKeys);
 }
 if (\is_array($item)) {
 return \array_reverse($item, $preserveKeys);
 }
 $string = (string) $item;
 if ('UTF-8' !== $charset) {
 $string = self::convertEncoding($string, 'UTF-8', $charset);
 }
 \preg_match_all('/./us', $string, $matches);
 $string = \implode('', \array_reverse($matches[0]));
 if ('UTF-8' !== $charset) {
 $string = self::convertEncoding($string, $charset, 'UTF-8');
 }
 return $string;
 }
 public static function sort(Environment $env, $array, $arrow = null) : array
 {
 if ($array instanceof \Traversable) {
 $array = \iterator_to_array($array);
 } elseif (!\is_array($array)) {
 throw new RuntimeError(\sprintf('The sort filter only works with arrays or "Traversable", got "%s".', \gettype($array)));
 }
 if (null !== $arrow) {
 self::checkArrowInSandbox($env, $arrow, 'sort', 'filter');
 \uasort($array, $arrow);
 } else {
 \asort($array);
 }
 return $array;
 }
 public static function inFilter($value, $compare)
 {
 if ($value instanceof Markup) {
 $value = (string) $value;
 }
 if ($compare instanceof Markup) {
 $compare = (string) $compare;
 }
 if (\is_string($compare)) {
 if (\is_string($value) || \is_int($value) || \is_float($value)) {
 return '' === $value || \str_contains($compare, (string) $value);
 }
 return \false;
 }
 if (!\is_iterable($compare)) {
 return \false;
 }
 if (\is_object($value) || \is_resource($value)) {
 if (!\is_array($compare)) {
 foreach ($compare as $item) {
 if ($item === $value) {
 return \true;
 }
 }
 return \false;
 }
 return \in_array($value, $compare, \true);
 }
 foreach ($compare as $item) {
 if (0 === self::compare($value, $item)) {
 return \true;
 }
 }
 return \false;
 }
 public static function compare($a, $b)
 {
 // int <=> string
 if (\is_int($a) && \is_string($b)) {
 $bTrim = \trim($b, " \t\n\r\v\f");
 if (!\is_numeric($bTrim)) {
 return (string) $a <=> $b;
 }
 if ((int) $bTrim == $bTrim) {
 return $a <=> (int) $bTrim;
 } else {
 return (float) $a <=> (float) $bTrim;
 }
 }
 if (\is_string($a) && \is_int($b)) {
 $aTrim = \trim($a, " \t\n\r\v\f");
 if (!\is_numeric($aTrim)) {
 return $a <=> (string) $b;
 }
 if ((int) $aTrim == $aTrim) {
 return (int) $aTrim <=> $b;
 } else {
 return (float) $aTrim <=> (float) $b;
 }
 }
 // float <=> string
 if (\is_float($a) && \is_string($b)) {
 if (\is_nan($a)) {
 return 1;
 }
 $bTrim = \trim($b, " \t\n\r\v\f");
 if (!\is_numeric($bTrim)) {
 return (string) $a <=> $b;
 }
 return $a <=> (float) $bTrim;
 }
 if (\is_string($a) && \is_float($b)) {
 if (\is_nan($b)) {
 return 1;
 }
 $aTrim = \trim($a, " \t\n\r\v\f");
 if (!\is_numeric($aTrim)) {
 return $a <=> (string) $b;
 }
 return (float) $aTrim <=> $b;
 }
 // fallback to <=>
 return $a <=> $b;
 }
 public static function matches(string $regexp, ?string $str) : int
 {
 \set_error_handler(function ($t, $m) use($regexp) {
 throw new RuntimeError(\sprintf('Regexp "%s" passed to "matches" is not valid', $regexp) . \substr($m, 12));
 });
 try {
 return \preg_match($regexp, $str ?? '');
 } finally {
 \restore_error_handler();
 }
 }
 public static function trim($string, $characterMask = null, $side = 'both') : string
 {
 if (null === $characterMask) {
 $characterMask = " \t\n\r\x00\v";
 }
 switch ($side) {
 case 'both':
 return \trim($string ?? '', $characterMask);
 case 'left':
 return \ltrim($string ?? '', $characterMask);
 case 'right':
 return \rtrim($string ?? '', $characterMask);
 default:
 throw new RuntimeError('Trimming side must be "left", "right" or "both".');
 }
 }
 public static function nl2br($string) : string
 {
 return \nl2br($string ?? '');
 }
 public static function spaceless($content) : string
 {
 return \trim(\preg_replace('/>\\s+</', '><', $content ?? ''));
 }
 public static function convertEncoding($string, $to, $from) : string
 {
 if (!\function_exists('iconv')) {
 throw new RuntimeError('Unable to convert encoding: required function iconv() does not exist. You should install ext-iconv or symfony/polyfill-iconv.');
 }
 return \iconv($from, $to, $string ?? '');
 }
 public static function length(string $charset, $thing) : int
 {
 if (null === $thing) {
 return 0;
 }
 if (\is_scalar($thing)) {
 return \mb_strlen($thing, $charset);
 }
 if ($thing instanceof \Countable || \is_array($thing) || $thing instanceof \SimpleXMLElement) {
 return \count($thing);
 }
 if ($thing instanceof \Traversable) {
 return \iterator_count($thing);
 }
 if (\method_exists($thing, '__toString')) {
 return \mb_strlen((string) $thing, $charset);
 }
 return 1;
 }
 public static function upper(string $charset, $string) : string
 {
 return \mb_strtoupper($string ?? '', $charset);
 }
 public static function lower(string $charset, $string) : string
 {
 return \mb_strtolower($string ?? '', $charset);
 }
 public static function striptags($string, $allowable_tags = null) : string
 {
 return \strip_tags($string ?? '', $allowable_tags);
 }
 public static function titleCase(string $charset, $string) : string
 {
 return \mb_convert_case($string ?? '', \MB_CASE_TITLE, $charset);
 }
 public static function capitalize(string $charset, $string) : string
 {
 return \mb_strtoupper(\mb_substr($string ?? '', 0, 1, $charset), $charset) . \mb_strtolower(\mb_substr($string ?? '', 1, null, $charset), $charset);
 }
 public static function callMacro(Template $template, string $method, array $args, int $lineno, array $context, Source $source)
 {
 if (!\method_exists($template, $method)) {
 $parent = $template;
 while ($parent = $parent->getParent($context)) {
 if (\method_exists($parent, $method)) {
 return $parent->{$method}(...$args);
 }
 }
 throw new RuntimeError(\sprintf('Macro "%s" is not defined in template "%s".', \substr($method, \strlen('macro_')), $template->getTemplateName()), $lineno, $source);
 }
 return $template->{$method}(...$args);
 }
 public static function ensureTraversable($seq)
 {
 if (\is_iterable($seq)) {
 return $seq;
 }
 return [];
 }
 public static function toArray($seq, $preserveKeys = \true)
 {
 if ($seq instanceof \Traversable) {
 return \iterator_to_array($seq, $preserveKeys);
 }
 if (!\is_array($seq)) {
 return $seq;
 }
 return $preserveKeys ? $seq : \array_values($seq);
 }
 public static function testEmpty($value) : bool
 {
 if ($value instanceof \Countable) {
 return 0 === \count($value);
 }
 if ($value instanceof \Traversable) {
 return !\iterator_count($value);
 }
 if (\is_object($value) && \method_exists($value, '__toString')) {
 return '' === (string) $value;
 }
 return '' === $value || \false === $value || null === $value || [] === $value;
 }
 public static function include(Environment $env, $context, $template, $variables = [], $withContext = \true, $ignoreMissing = \false, $sandboxed = \false) : string
 {
 $alreadySandboxed = \false;
 $sandbox = null;
 if ($withContext) {
 $variables = \array_merge($context, $variables);
 }
 if ($isSandboxed = $sandboxed && $env->hasExtension(SandboxExtension::class)) {
 $sandbox = $env->getExtension(SandboxExtension::class);
 if (!($alreadySandboxed = $sandbox->isSandboxed())) {
 $sandbox->enableSandbox();
 }
 foreach (\is_array($template) ? $template : [$template] as $name) {
 // if a Template instance is passed, it might have been instantiated outside of a sandbox, check security
 if ($name instanceof TemplateWrapper || $name instanceof Template) {
 $name->unwrap()->checkSecurity();
 }
 }
 }
 try {
 $loaded = null;
 try {
 $loaded = $env->resolveTemplate($template);
 } catch (LoaderError $e) {
 if (!$ignoreMissing) {
 throw $e;
 }
 }
 return $loaded ? $loaded->render($variables) : '';
 } finally {
 if ($isSandboxed && !$alreadySandboxed) {
 $sandbox->disableSandbox();
 }
 }
 }
 public static function source(Environment $env, $name, $ignoreMissing = \false) : string
 {
 $loader = $env->getLoader();
 try {
 return $loader->getSourceContext($name)->getCode();
 } catch (LoaderError $e) {
 if (!$ignoreMissing) {
 throw $e;
 }
 return '';
 }
 }
 public static function constant($constant, $object = null)
 {
 if (null !== $object) {
 if ('class' === $constant) {
 return \get_class($object);
 }
 $constant = \get_class($object) . '::' . $constant;
 }
 if (!\defined($constant)) {
 if ('::class' === \strtolower(\substr($constant, -7))) {
 throw new RuntimeError(\sprintf('You cannot use the Twig function "constant()" to access "%s". You could provide an object and call constant("class", $object) or use the class name directly as a string.', $constant));
 }
 throw new RuntimeError(\sprintf('Constant "%s" is undefined.', $constant));
 }
 return \constant($constant);
 }
 public static function constantIsDefined($constant, $object = null) : bool
 {
 if (null !== $object) {
 if ('class' === $constant) {
 return \true;
 }
 $constant = \get_class($object) . '::' . $constant;
 }
 return \defined($constant);
 }
 public static function batch($items, $size, $fill = null, $preserveKeys = \true) : array
 {
 if (!\is_iterable($items)) {
 throw new RuntimeError(\sprintf('The "batch" filter expects an array or "Traversable", got "%s".', \is_object($items) ? \get_class($items) : \gettype($items)));
 }
 $size = \ceil($size);
 $result = \array_chunk(self::toArray($items, $preserveKeys), $size, $preserveKeys);
 if (null !== $fill && $result) {
 $last = \count($result) - 1;
 if ($fillCount = $size - \count($result[$last])) {
 for ($i = 0; $i < $fillCount; ++$i) {
 $result[$last][] = $fill;
 }
 }
 }
 return $result;
 }
 public static function getAttribute(Environment $env, Source $source, $object, $item, array $arguments = [], $type = 'any', $isDefinedTest = \false, $ignoreStrictCheck = \false, $sandboxed = \false, int $lineno = -1)
 {
 // array
 if ('method' !== $type) {
 $arrayItem = \is_bool($item) || \is_float($item) ? (int) $item : $item;
 if ((\is_array($object) || $object instanceof \ArrayObject) && (isset($object[$arrayItem]) || \array_key_exists($arrayItem, (array) $object)) || $object instanceof \ArrayAccess && isset($object[$arrayItem])) {
 if ($isDefinedTest) {
 return \true;
 }
 return $object[$arrayItem];
 }
 if ('array' === $type || !\is_object($object)) {
 if ($isDefinedTest) {
 return \false;
 }
 if ($ignoreStrictCheck || !$env->isStrictVariables()) {
 return;
 }
 if ($object instanceof \ArrayAccess) {
 $message = \sprintf('Key "%s" in object with ArrayAccess of class "%s" does not exist.', $arrayItem, \get_class($object));
 } elseif (\is_object($object)) {
 $message = \sprintf('Impossible to access a key "%s" on an object of class "%s" that does not implement ArrayAccess interface.', $item, \get_class($object));
 } elseif (\is_array($object)) {
 if (empty($object)) {
 $message = \sprintf('Key "%s" does not exist as the array is empty.', $arrayItem);
 } else {
 $message = \sprintf('Key "%s" for array with keys "%s" does not exist.', $arrayItem, \implode(', ', \array_keys($object)));
 }
 } elseif ('array' === $type) {
 if (null === $object) {
 $message = \sprintf('Impossible to access a key ("%s") on a null variable.', $item);
 } else {
 $message = \sprintf('Impossible to access a key ("%s") on a %s variable ("%s").', $item, \gettype($object), $object);
 }
 } elseif (null === $object) {
 $message = \sprintf('Impossible to access an attribute ("%s") on a null variable.', $item);
 } else {
 $message = \sprintf('Impossible to access an attribute ("%s") on a %s variable ("%s").', $item, \gettype($object), $object);
 }
 throw new RuntimeError($message, $lineno, $source);
 }
 }
 if (!\is_object($object)) {
 if ($isDefinedTest) {
 return \false;
 }
 if ($ignoreStrictCheck || !$env->isStrictVariables()) {
 return;
 }
 if (null === $object) {
 $message = \sprintf('Impossible to invoke a method ("%s") on a null variable.', $item);
 } elseif (\is_array($object)) {
 $message = \sprintf('Impossible to invoke a method ("%s") on an array.', $item);
 } else {
 $message = \sprintf('Impossible to invoke a method ("%s") on a %s variable ("%s").', $item, \gettype($object), $object);
 }
 throw new RuntimeError($message, $lineno, $source);
 }
 if ($object instanceof Template) {
 throw new RuntimeError('Accessing \\Twig\\Template attributes is forbidden.', $lineno, $source);
 }
 // object property
 if ('method' !== $type) {
 if (isset($object->{$item}) || \array_key_exists((string) $item, (array) $object)) {
 if ($isDefinedTest) {
 return \true;
 }
 if ($sandboxed) {
 $env->getExtension(SandboxExtension::class)->checkPropertyAllowed($object, $item, $lineno, $source);
 }
 return $object->{$item};
 }
 }
 static $cache = [];
 $class = \get_class($object);
 // object method
 // precedence: getXxx() > isXxx() > hasXxx()
 if (!isset($cache[$class])) {
 $methods = \get_class_methods($object);
 \sort($methods);
 $lcMethods = \array_map(function ($value) {
 return \strtr($value, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
 }, $methods);
 $classCache = [];
 foreach ($methods as $i => $method) {
 $classCache[$method] = $method;
 $classCache[$lcName = $lcMethods[$i]] = $method;
 if ('g' === $lcName[0] && \str_starts_with($lcName, 'get')) {
 $name = \substr($method, 3);
 $lcName = \substr($lcName, 3);
 } elseif ('i' === $lcName[0] && \str_starts_with($lcName, 'is')) {
 $name = \substr($method, 2);
 $lcName = \substr($lcName, 2);
 } elseif ('h' === $lcName[0] && \str_starts_with($lcName, 'has')) {
 $name = \substr($method, 3);
 $lcName = \substr($lcName, 3);
 if (\in_array('is' . $lcName, $lcMethods)) {
 continue;
 }
 } else {
 continue;
 }
 // skip get() and is() methods (in which case, $name is empty)
 if ($name) {
 if (!isset($classCache[$name])) {
 $classCache[$name] = $method;
 }
 if (!isset($classCache[$lcName])) {
 $classCache[$lcName] = $method;
 }
 }
 }
 $cache[$class] = $classCache;
 }
 $call = \false;
 if (isset($cache[$class][$item])) {
 $method = $cache[$class][$item];
 } elseif (isset($cache[$class][$lcItem = \strtr($item, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')])) {
 $method = $cache[$class][$lcItem];
 } elseif (isset($cache[$class]['__call'])) {
 $method = $item;
 $call = \true;
 } else {
 if ($isDefinedTest) {
 return \false;
 }
 if ($ignoreStrictCheck || !$env->isStrictVariables()) {
 return;
 }
 throw new RuntimeError(\sprintf('Neither the property "%1$s" nor one of the methods "%1$s()", "get%1$s()"/"is%1$s()"/"has%1$s()" or "__call()" exist and have public access in class "%2$s".', $item, $class), $lineno, $source);
 }
 if ($isDefinedTest) {
 return \true;
 }
 if ($sandboxed) {
 $env->getExtension(SandboxExtension::class)->checkMethodAllowed($object, $method, $lineno, $source);
 }
 // Some objects throw exceptions when they have __call, and the method we try
 // to call is not supported. If ignoreStrictCheck is true, we should return null.
 try {
 $ret = $object->{$method}(...$arguments);
 } catch (\BadMethodCallException $e) {
 if ($call && ($ignoreStrictCheck || !$env->isStrictVariables())) {
 return;
 }
 throw $e;
 }
 return $ret;
 }
 public static function column($array, $name, $index = null) : array
 {
 if ($array instanceof \Traversable) {
 $array = \iterator_to_array($array);
 } elseif (!\is_array($array)) {
 throw new RuntimeError(\sprintf('The column filter only works with arrays or "Traversable", got "%s" as first argument.', \gettype($array)));
 }
 return \array_column($array, $name, $index);
 }
 public static function filter(Environment $env, $array, $arrow)
 {
 if (!\is_iterable($array)) {
 throw new RuntimeError(\sprintf('The "filter" filter expects an array or "Traversable", got "%s".', \is_object($array) ? \get_class($array) : \gettype($array)));
 }
 self::checkArrowInSandbox($env, $arrow, 'filter', 'filter');
 if (\is_array($array)) {
 return \array_filter($array, $arrow, \ARRAY_FILTER_USE_BOTH);
 }
 // the IteratorIterator wrapping is needed as some internal PHP classes are \Traversable but do not implement \Iterator
 return new \CallbackFilterIterator(new \IteratorIterator($array), $arrow);
 }
 public static function map(Environment $env, $array, $arrow)
 {
 self::checkArrowInSandbox($env, $arrow, 'map', 'filter');
 $r = [];
 foreach ($array as $k => $v) {
 $r[$k] = $arrow($v, $k);
 }
 return $r;
 }
 public static function reduce(Environment $env, $array, $arrow, $initial = null)
 {
 self::checkArrowInSandbox($env, $arrow, 'reduce', 'filter');
 if (!\is_array($array) && !$array instanceof \Traversable) {
 throw new RuntimeError(\sprintf('The "reduce" filter only works with arrays or "Traversable", got "%s" as first argument.', \gettype($array)));
 }
 $accumulator = $initial;
 foreach ($array as $key => $value) {
 $accumulator = $arrow($accumulator, $value, $key);
 }
 return $accumulator;
 }
 public static function arraySome(Environment $env, $array, $arrow)
 {
 self::checkArrowInSandbox($env, $arrow, 'has some', 'operator');
 foreach ($array as $k => $v) {
 if ($arrow($v, $k)) {
 return \true;
 }
 }
 return \false;
 }
 public static function arrayEvery(Environment $env, $array, $arrow)
 {
 self::checkArrowInSandbox($env, $arrow, 'has every', 'operator');
 foreach ($array as $k => $v) {
 if (!$arrow($v, $k)) {
 return \false;
 }
 }
 return \true;
 }
 public static function checkArrowInSandbox(Environment $env, $arrow, $thing, $type)
 {
 if (!$arrow instanceof \Closure && $env->hasExtension(SandboxExtension::class) && $env->getExtension(SandboxExtension::class)->isSandboxed()) {
 throw new RuntimeError(\sprintf('The callable passed to the "%s" %s must be a Closure in sandbox mode.', $thing, $type));
 }
 }
 public static function captureOutput(iterable $body) : string
 {
 $output = '';
 $level = \ob_get_level();
 \ob_start();
 try {
 foreach ($body as $data) {
 if (\ob_get_length()) {
 $output .= \ob_get_clean();
 \ob_start();
 }
 $output .= $data;
 }
 if (\ob_get_length()) {
 $output .= \ob_get_clean();
 }
 } finally {
 while (\ob_get_level() > $level) {
 \ob_end_clean();
 }
 }
 return $output;
 }
}
