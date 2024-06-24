<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\FileExtensionEscapingStrategy;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\NodeVisitor\EscaperNodeVisitor;
use MailPoetVendor\Twig\Runtime\EscaperRuntime;
use MailPoetVendor\Twig\TokenParser\AutoEscapeTokenParser;
use MailPoetVendor\Twig\TwigFilter;
final class EscaperExtension extends AbstractExtension
{
 private $environment;
 private $escapers = [];
 private $escaper;
 private $defaultStrategy;
 public function __construct($defaultStrategy = 'html')
 {
 $this->setDefaultStrategy($defaultStrategy);
 }
 public function getTokenParsers() : array
 {
 return [new AutoEscapeTokenParser()];
 }
 public function getNodeVisitors() : array
 {
 return [new EscaperNodeVisitor()];
 }
 public function getFilters() : array
 {
 return [new TwigFilter('escape', [EscaperRuntime::class, 'escape'], ['is_safe_callback' => [self::class, 'escapeFilterIsSafe']]), new TwigFilter('e', [EscaperRuntime::class, 'escape'], ['is_safe_callback' => [self::class, 'escapeFilterIsSafe']]), new TwigFilter('raw', [self::class, 'raw'], ['is_safe' => ['all']])];
 }
 public function setEnvironment(Environment $environment, bool $triggerDeprecation = \true) : void
 {
 if ($triggerDeprecation) {
 trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated and not needed if you are using methods from "Twig\\Runtime\\EscaperRuntime".', __METHOD__);
 }
 $this->environment = $environment;
 $this->escaper = $environment->getRuntime(EscaperRuntime::class);
 }
 public function setEscaperRuntime(EscaperRuntime $escaper)
 {
 trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated and not needed if you are using methods from "Twig\\Runtime\\EscaperRuntime".', __METHOD__);
 $this->escaper = $escaper;
 }
 public function setDefaultStrategy($defaultStrategy) : void
 {
 if ('name' === $defaultStrategy) {
 $defaultStrategy = [FileExtensionEscapingStrategy::class, 'guess'];
 }
 $this->defaultStrategy = $defaultStrategy;
 }
 public function getDefaultStrategy(string $name)
 {
 // disable string callables to avoid calling a function named html or js,
 // or any other upcoming escaping strategy
 if (!\is_string($this->defaultStrategy) && \false !== $this->defaultStrategy) {
 return \call_user_func($this->defaultStrategy, $name);
 }
 return $this->defaultStrategy;
 }
 public function setEscaper($strategy, callable $callable)
 {
 trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated, use the "Twig\\Runtime\\EscaperRuntime::setEscaper()" method instead (be warned that Environment is not passed anymore to the callable).', __METHOD__);
 if (!isset($this->environment)) {
 throw new \LogicException(\sprintf('You must call "setEnvironment()" before calling "%s()".', __METHOD__));
 }
 $this->escapers[$strategy] = $callable;
 $callable = function ($string, $charset) use($callable) {
 return $callable($this->environment, $string, $charset);
 };
 $this->escaper->setEscaper($strategy, $callable);
 }
 public function getEscapers()
 {
 trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated, use the "Twig\\Runtime\\EscaperRuntime::getEscaper()" method instead.', __METHOD__);
 return $this->escapers;
 }
 public function setSafeClasses(array $safeClasses = [])
 {
 trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated, use the "Twig\\Runtime\\EscaperRuntime::setSafeClasses()" method instead.', __METHOD__);
 if (!isset($this->escaper)) {
 throw new \LogicException(\sprintf('You must call "setEnvironment()" before calling "%s()".', __METHOD__));
 }
 $this->escaper->setSafeClasses($safeClasses);
 }
 public function addSafeClass(string $class, array $strategies)
 {
 trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated, use the "Twig\\Runtime\\EscaperRuntime::addSafeClass()" method instead.', __METHOD__);
 if (!isset($this->escaper)) {
 throw new \LogicException(\sprintf('You must call "setEnvironment()" before calling "%s()".', __METHOD__));
 }
 $this->escaper->addSafeClass($class, $strategies);
 }
 public static function raw($string)
 {
 return $string;
 }
 public static function escapeFilterIsSafe(Node $filterArgs)
 {
 foreach ($filterArgs as $arg) {
 if ($arg instanceof ConstantExpression) {
 return [$arg->getAttribute('value')];
 }
 return [];
 }
 return ['html'];
 }
}
