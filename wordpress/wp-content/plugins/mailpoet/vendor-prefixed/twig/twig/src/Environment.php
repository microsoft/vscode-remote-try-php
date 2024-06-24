<?php
namespace MailPoetVendor\Twig;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Cache\CacheInterface;
use MailPoetVendor\Twig\Cache\FilesystemCache;
use MailPoetVendor\Twig\Cache\NullCache;
use MailPoetVendor\Twig\Error\Error;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Extension\CoreExtension;
use MailPoetVendor\Twig\Extension\EscaperExtension;
use MailPoetVendor\Twig\Extension\ExtensionInterface;
use MailPoetVendor\Twig\Extension\OptimizerExtension;
use MailPoetVendor\Twig\Extension\YieldNotReadyExtension;
use MailPoetVendor\Twig\Loader\ArrayLoader;
use MailPoetVendor\Twig\Loader\ChainLoader;
use MailPoetVendor\Twig\Loader\LoaderInterface;
use MailPoetVendor\Twig\Node\Expression\Binary\AbstractBinary;
use MailPoetVendor\Twig\Node\Expression\Unary\AbstractUnary;
use MailPoetVendor\Twig\Node\ModuleNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\NodeVisitor\NodeVisitorInterface;
use MailPoetVendor\Twig\Runtime\EscaperRuntime;
use MailPoetVendor\Twig\RuntimeLoader\FactoryRuntimeLoader;
use MailPoetVendor\Twig\RuntimeLoader\RuntimeLoaderInterface;
use MailPoetVendor\Twig\TokenParser\TokenParserInterface;
class Environment
{
 public const VERSION = '3.10.3';
 public const VERSION_ID = 301003;
 public const MAJOR_VERSION = 3;
 public const MINOR_VERSION = 10;
 public const RELEASE_VERSION = 3;
 public const EXTRA_VERSION = '';
 private $charset;
 private $loader;
 private $debug;
 private $autoReload;
 private $cache;
 private $lexer;
 private $parser;
 private $compiler;
 private $globals = [];
 private $resolvedGlobals;
 private $loadedTemplates;
 private $strictVariables;
 private $templateClassPrefix = '__TwigTemplate_';
 private $originalCache;
 private $extensionSet;
 private $runtimeLoaders = [];
 private $runtimes = [];
 private $optionsHash;
 private $useYield;
 private $defaultRuntimeLoader;
 public function __construct(LoaderInterface $loader, $options = [])
 {
 $this->setLoader($loader);
 $options = \array_merge(['debug' => \false, 'charset' => 'UTF-8', 'strict_variables' => \false, 'autoescape' => 'html', 'cache' => \false, 'auto_reload' => null, 'optimizations' => -1, 'use_yield' => \false], $options);
 $this->useYield = (bool) $options['use_yield'];
 $this->debug = (bool) $options['debug'];
 $this->setCharset($options['charset'] ?? 'UTF-8');
 $this->autoReload = null === $options['auto_reload'] ? $this->debug : (bool) $options['auto_reload'];
 $this->strictVariables = (bool) $options['strict_variables'];
 $this->setCache($options['cache']);
 $this->extensionSet = new ExtensionSet();
 $this->defaultRuntimeLoader = new FactoryRuntimeLoader([EscaperRuntime::class => function () {
 return new EscaperRuntime($this->charset);
 }]);
 $this->addExtension(new CoreExtension());
 $escaperExt = new EscaperExtension($options['autoescape']);
 $escaperExt->setEnvironment($this, \false);
 $this->addExtension($escaperExt);
 if (\PHP_VERSION_ID >= 80000) {
 $this->addExtension(new YieldNotReadyExtension($this->useYield));
 }
 $this->addExtension(new OptimizerExtension($options['optimizations']));
 }
 public function useYield() : bool
 {
 return $this->useYield;
 }
 public function enableDebug()
 {
 $this->debug = \true;
 $this->updateOptionsHash();
 }
 public function disableDebug()
 {
 $this->debug = \false;
 $this->updateOptionsHash();
 }
 public function isDebug()
 {
 return $this->debug;
 }
 public function enableAutoReload()
 {
 $this->autoReload = \true;
 }
 public function disableAutoReload()
 {
 $this->autoReload = \false;
 }
 public function isAutoReload()
 {
 return $this->autoReload;
 }
 public function enableStrictVariables()
 {
 $this->strictVariables = \true;
 $this->updateOptionsHash();
 }
 public function disableStrictVariables()
 {
 $this->strictVariables = \false;
 $this->updateOptionsHash();
 }
 public function isStrictVariables()
 {
 return $this->strictVariables;
 }
 public function getCache($original = \true)
 {
 return $original ? $this->originalCache : $this->cache;
 }
 public function setCache($cache)
 {
 if (\is_string($cache)) {
 $this->originalCache = $cache;
 $this->cache = new FilesystemCache($cache, $this->autoReload ? FilesystemCache::FORCE_BYTECODE_INVALIDATION : 0);
 } elseif (\false === $cache) {
 $this->originalCache = $cache;
 $this->cache = new NullCache();
 } elseif ($cache instanceof CacheInterface) {
 $this->originalCache = $this->cache = $cache;
 } else {
 throw new \LogicException('Cache can only be a string, false, or a \\Twig\\Cache\\CacheInterface implementation.');
 }
 }
 public function getTemplateClass(string $name, ?int $index = null) : string
 {
 $key = $this->getLoader()->getCacheKey($name) . $this->optionsHash;
 return $this->templateClassPrefix . \hash(\PHP_VERSION_ID < 80100 ? 'sha256' : 'xxh128', $key) . (null === $index ? '' : '___' . $index);
 }
 public function render($name, array $context = []) : string
 {
 return $this->load($name)->render($context);
 }
 public function display($name, array $context = []) : void
 {
 $this->load($name)->display($context);
 }
 public function load($name) : TemplateWrapper
 {
 if ($name instanceof TemplateWrapper) {
 return $name;
 }
 if ($name instanceof Template) {
 trigger_deprecation('twig/twig', '3.9', 'Passing a "%s" instance to "%s" is deprecated.', self::class, __METHOD__);
 return $name;
 }
 return new TemplateWrapper($this, $this->loadTemplate($this->getTemplateClass($name), $name));
 }
 public function loadTemplate(string $cls, string $name, ?int $index = null) : Template
 {
 $mainCls = $cls;
 if (null !== $index) {
 $cls .= '___' . $index;
 }
 if (isset($this->loadedTemplates[$cls])) {
 return $this->loadedTemplates[$cls];
 }
 if (!\class_exists($cls, \false)) {
 $key = $this->cache->generateKey($name, $mainCls);
 if (!$this->isAutoReload() || $this->isTemplateFresh($name, $this->cache->getTimestamp($key))) {
 $this->cache->load($key);
 }
 if (!\class_exists($cls, \false)) {
 $source = $this->getLoader()->getSourceContext($name);
 $content = $this->compileSource($source);
 $this->cache->write($key, $content);
 $this->cache->load($key);
 if (!\class_exists($mainCls, \false)) {
 eval('?>' . $content);
 }
 if (!\class_exists($cls, \false)) {
 throw new RuntimeError(\sprintf('Failed to load Twig template "%s", index "%s": cache might be corrupted.', $name, $index), -1, $source);
 }
 }
 }
 $this->extensionSet->initRuntime();
 return $this->loadedTemplates[$cls] = new $cls($this);
 }
 public function createTemplate(string $template, ?string $name = null) : TemplateWrapper
 {
 $hash = \hash(\PHP_VERSION_ID < 80100 ? 'sha256' : 'xxh128', $template, \false);
 if (null !== $name) {
 $name = \sprintf('%s (string template %s)', $name, $hash);
 } else {
 $name = \sprintf('__string_template__%s', $hash);
 }
 $loader = new ChainLoader([new ArrayLoader([$name => $template]), $current = $this->getLoader()]);
 $this->setLoader($loader);
 try {
 return new TemplateWrapper($this, $this->loadTemplate($this->getTemplateClass($name), $name));
 } finally {
 $this->setLoader($current);
 }
 }
 public function isTemplateFresh(string $name, int $time) : bool
 {
 return $this->extensionSet->getLastModified() <= $time && $this->getLoader()->isFresh($name, $time);
 }
 public function resolveTemplate($names) : TemplateWrapper
 {
 if (!\is_array($names)) {
 return $this->load($names);
 }
 $count = \count($names);
 foreach ($names as $name) {
 if ($name instanceof Template) {
 trigger_deprecation('twig/twig', '3.9', 'Passing a "%s" instance to "%s" is deprecated.', Template::class, __METHOD__);
 return new TemplateWrapper($this, $name);
 }
 if ($name instanceof TemplateWrapper) {
 return $name;
 }
 if (1 !== $count && !$this->getLoader()->exists($name)) {
 continue;
 }
 return $this->load($name);
 }
 throw new LoaderError(\sprintf('Unable to find one of the following templates: "%s".', \implode('", "', $names)));
 }
 public function setLexer(Lexer $lexer)
 {
 $this->lexer = $lexer;
 }
 public function tokenize(Source $source) : TokenStream
 {
 if (null === $this->lexer) {
 $this->lexer = new Lexer($this);
 }
 return $this->lexer->tokenize($source);
 }
 public function setParser(Parser $parser)
 {
 $this->parser = $parser;
 }
 public function parse(TokenStream $stream) : ModuleNode
 {
 if (null === $this->parser) {
 $this->parser = new Parser($this);
 }
 return $this->parser->parse($stream);
 }
 public function setCompiler(Compiler $compiler)
 {
 $this->compiler = $compiler;
 }
 public function compile(Node $node) : string
 {
 if (null === $this->compiler) {
 $this->compiler = new Compiler($this);
 }
 return $this->compiler->compile($node)->getSource();
 }
 public function compileSource(Source $source) : string
 {
 try {
 return $this->compile($this->parse($this->tokenize($source)));
 } catch (Error $e) {
 $e->setSourceContext($source);
 throw $e;
 } catch (\Exception $e) {
 throw new SyntaxError(\sprintf('An exception has been thrown during the compilation of a template ("%s").', $e->getMessage()), -1, $source, $e);
 }
 }
 public function setLoader(LoaderInterface $loader)
 {
 $this->loader = $loader;
 }
 public function getLoader() : LoaderInterface
 {
 return $this->loader;
 }
 public function setCharset(string $charset)
 {
 if ('UTF8' === ($charset = \strtoupper($charset ?: ''))) {
 // iconv on Windows requires "UTF-8" instead of "UTF8"
 $charset = 'UTF-8';
 }
 $this->charset = $charset;
 }
 public function getCharset() : string
 {
 return $this->charset;
 }
 public function hasExtension(string $class) : bool
 {
 return $this->extensionSet->hasExtension($class);
 }
 public function addRuntimeLoader(RuntimeLoaderInterface $loader)
 {
 $this->runtimeLoaders[] = $loader;
 }
 public function getExtension(string $class) : ExtensionInterface
 {
 return $this->extensionSet->getExtension($class);
 }
 public function getRuntime(string $class)
 {
 if (isset($this->runtimes[$class])) {
 return $this->runtimes[$class];
 }
 foreach ($this->runtimeLoaders as $loader) {
 if (null !== ($runtime = $loader->load($class))) {
 return $this->runtimes[$class] = $runtime;
 }
 }
 if (null !== ($runtime = $this->defaultRuntimeLoader->load($class))) {
 return $this->runtimes[$class] = $runtime;
 }
 throw new RuntimeError(\sprintf('Unable to load the "%s" runtime.', $class));
 }
 public function addExtension(ExtensionInterface $extension)
 {
 $this->extensionSet->addExtension($extension);
 $this->updateOptionsHash();
 }
 public function setExtensions(array $extensions)
 {
 $this->extensionSet->setExtensions($extensions);
 $this->updateOptionsHash();
 }
 public function getExtensions() : array
 {
 return $this->extensionSet->getExtensions();
 }
 public function addTokenParser(TokenParserInterface $parser)
 {
 $this->extensionSet->addTokenParser($parser);
 }
 public function getTokenParsers() : array
 {
 return $this->extensionSet->getTokenParsers();
 }
 public function getTokenParser(string $name) : ?TokenParserInterface
 {
 return $this->extensionSet->getTokenParser($name);
 }
 public function registerUndefinedTokenParserCallback(callable $callable) : void
 {
 $this->extensionSet->registerUndefinedTokenParserCallback($callable);
 }
 public function addNodeVisitor(NodeVisitorInterface $visitor)
 {
 $this->extensionSet->addNodeVisitor($visitor);
 }
 public function getNodeVisitors() : array
 {
 return $this->extensionSet->getNodeVisitors();
 }
 public function addFilter(TwigFilter $filter)
 {
 $this->extensionSet->addFilter($filter);
 }
 public function getFilter(string $name) : ?TwigFilter
 {
 return $this->extensionSet->getFilter($name);
 }
 public function registerUndefinedFilterCallback(callable $callable) : void
 {
 $this->extensionSet->registerUndefinedFilterCallback($callable);
 }
 public function getFilters() : array
 {
 return $this->extensionSet->getFilters();
 }
 public function addTest(TwigTest $test)
 {
 $this->extensionSet->addTest($test);
 }
 public function getTests() : array
 {
 return $this->extensionSet->getTests();
 }
 public function getTest(string $name) : ?TwigTest
 {
 return $this->extensionSet->getTest($name);
 }
 public function addFunction(TwigFunction $function)
 {
 $this->extensionSet->addFunction($function);
 }
 public function getFunction(string $name) : ?TwigFunction
 {
 return $this->extensionSet->getFunction($name);
 }
 public function registerUndefinedFunctionCallback(callable $callable) : void
 {
 $this->extensionSet->registerUndefinedFunctionCallback($callable);
 }
 public function getFunctions() : array
 {
 return $this->extensionSet->getFunctions();
 }
 public function addGlobal(string $name, $value)
 {
 if ($this->extensionSet->isInitialized() && !\array_key_exists($name, $this->getGlobals())) {
 throw new \LogicException(\sprintf('Unable to add global "%s" as the runtime or the extensions have already been initialized.', $name));
 }
 if (null !== $this->resolvedGlobals) {
 $this->resolvedGlobals[$name] = $value;
 } else {
 $this->globals[$name] = $value;
 }
 }
 public function getGlobals() : array
 {
 if ($this->extensionSet->isInitialized()) {
 if (null === $this->resolvedGlobals) {
 $this->resolvedGlobals = \array_merge($this->extensionSet->getGlobals(), $this->globals);
 }
 return $this->resolvedGlobals;
 }
 return \array_merge($this->extensionSet->getGlobals(), $this->globals);
 }
 public function mergeGlobals(array $context) : array
 {
 // we don't use array_merge as the context being generally
 // bigger than globals, this code is faster.
 foreach ($this->getGlobals() as $key => $value) {
 if (!\array_key_exists($key, $context)) {
 $context[$key] = $value;
 }
 }
 return $context;
 }
 public function getUnaryOperators() : array
 {
 return $this->extensionSet->getUnaryOperators();
 }
 public function getBinaryOperators() : array
 {
 return $this->extensionSet->getBinaryOperators();
 }
 private function updateOptionsHash() : void
 {
 $this->optionsHash = \implode(':', [$this->extensionSet->getSignature(), \PHP_MAJOR_VERSION, \PHP_MINOR_VERSION, self::VERSION, (int) $this->debug, (int) $this->strictVariables, $this->useYield ? '1' : '0']);
 }
}
