<?php
namespace MailPoetVendor\Doctrine\Common\Proxy;
if (!defined('ABSPATH')) exit;
use BackedEnum;
use MailPoetVendor\Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use MailPoetVendor\Doctrine\Common\Proxy\Exception\UnexpectedValueException;
use MailPoetVendor\Doctrine\Common\Util\ClassUtils;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use function array_combine;
use function array_diff;
use function array_key_exists;
use function array_map;
use function array_slice;
use function array_unique;
use function assert;
use function call_user_func;
use function chmod;
use function class_exists;
use function dirname;
use function explode;
use function file;
use function file_put_contents;
use function get_class;
use function implode;
use function in_array;
use function interface_exists;
use function is_callable;
use function is_dir;
use function is_string;
use function is_writable;
use function lcfirst;
use function ltrim;
use function method_exists;
use function mkdir;
use function preg_match;
use function preg_match_all;
use function rename;
use function rtrim;
use function sprintf;
use function str_replace;
use function strrev;
use function strtolower;
use function strtr;
use function substr;
use function trim;
use function uniqid;
use function var_export;
use const DIRECTORY_SEPARATOR;
use const PHP_VERSION_ID;
class ProxyGenerator
{
 public const PATTERN_MATCH_ID_METHOD = '((public\\s+)?(function\\s+%s\\s*\\(\\)\\s*)\\s*(?::\\s*\\??\\s*\\\\?[a-z_\\x7f-\\xff][\\w\\x7f-\\xff]*(?:\\\\[a-z_\\x7f-\\xff][\\w\\x7f-\\xff]*)*\\s*)?{\\s*return\\s*\\$this->%s;\\s*})i';
 private $proxyNamespace;
 private $proxyDirectory;
 protected $placeholders = ['baseProxyInterface' => Proxy::class, 'additionalProperties' => ''];
 protected $proxyClassTemplate = '<?php
namespace <namespace>;
<enumUseStatements>
class <proxyShortClassName> extends \\<className> implements \\<baseProxyInterface>
{
 public $__initializer__;
 public $__cloner__;
 public $__isInitialized__ = false;
 public static $lazyPropertiesNames = <lazyPropertiesNames>;
 public static $lazyPropertiesDefaults = <lazyPropertiesDefaults>;
<additionalProperties>
<constructorImpl>
<magicGet>
<magicSet>
<magicIsset>
<sleepImpl>
<wakeupImpl>
<cloneImpl>
 public function __load()
 {
 $this->__initializer__ && $this->__initializer__->__invoke($this, \'__load\', []);
 }
 public function __isInitialized()
 {
 return $this->__isInitialized__;
 }
 public function __setInitialized($initialized)
 {
 $this->__isInitialized__ = $initialized;
 }
 public function __setInitializer(\\Closure $initializer = null)
 {
 $this->__initializer__ = $initializer;
 }
 public function __getInitializer()
 {
 return $this->__initializer__;
 }
 public function __setCloner(\\Closure $cloner = null)
 {
 $this->__cloner__ = $cloner;
 }
 public function __getCloner()
 {
 return $this->__cloner__;
 }
 public function __getLazyProperties()
 {
 return self::$lazyPropertiesDefaults;
 }
 <methods>
}
';
 public function __construct($proxyDirectory, $proxyNamespace)
 {
 if (!$proxyDirectory) {
 throw InvalidArgumentException::proxyDirectoryRequired();
 }
 if (!$proxyNamespace) {
 throw InvalidArgumentException::proxyNamespaceRequired();
 }
 $this->proxyDirectory = $proxyDirectory;
 $this->proxyNamespace = $proxyNamespace;
 }
 public function setPlaceholder($name, $placeholder)
 {
 if (!is_string($placeholder) && !is_callable($placeholder)) {
 throw InvalidArgumentException::invalidPlaceholder($name);
 }
 $this->placeholders[$name] = $placeholder;
 }
 public function setProxyClassTemplate($proxyClassTemplate)
 {
 $this->proxyClassTemplate = (string) $proxyClassTemplate;
 }
 public function generateProxyClass(ClassMetadata $class, $fileName = \false)
 {
 $this->verifyClassCanBeProxied($class);
 preg_match_all('(<([a-zA-Z]+)>)', $this->proxyClassTemplate, $placeholderMatches);
 $placeholderMatches = array_combine($placeholderMatches[0], $placeholderMatches[1]);
 $placeholders = [];
 foreach ($placeholderMatches as $placeholder => $name) {
 $placeholders[$placeholder] = $this->placeholders[$name] ?? [$this, 'generate' . $name];
 }
 foreach ($placeholders as &$placeholder) {
 if (!is_callable($placeholder)) {
 continue;
 }
 $placeholder = call_user_func($placeholder, $class);
 }
 $proxyCode = strtr($this->proxyClassTemplate, $placeholders);
 if (!$fileName) {
 $proxyClassName = $this->generateNamespace($class) . '\\' . $this->generateProxyShortClassName($class);
 if (!class_exists($proxyClassName)) {
 eval(substr($proxyCode, 5));
 }
 return;
 }
 $parentDirectory = dirname($fileName);
 if (!is_dir($parentDirectory) && @mkdir($parentDirectory, 0775, \true) === \false) {
 throw UnexpectedValueException::proxyDirectoryNotWritable($this->proxyDirectory);
 }
 if (!is_writable($parentDirectory)) {
 throw UnexpectedValueException::proxyDirectoryNotWritable($this->proxyDirectory);
 }
 $tmpFileName = $fileName . '.' . uniqid('', \true);
 file_put_contents($tmpFileName, $proxyCode);
 @chmod($tmpFileName, 0664);
 rename($tmpFileName, $fileName);
 }
 private function verifyClassCanBeProxied(ClassMetadata $class)
 {
 if ($class->getReflectionClass()->isFinal()) {
 throw InvalidArgumentException::classMustNotBeFinal($class->getName());
 }
 if ($class->getReflectionClass()->isAbstract()) {
 throw InvalidArgumentException::classMustNotBeAbstract($class->getName());
 }
 }
 private function generateProxyShortClassName(ClassMetadata $class)
 {
 $proxyClassName = ClassUtils::generateProxyClassName($class->getName(), $this->proxyNamespace);
 $parts = explode('\\', strrev($proxyClassName), 2);
 return strrev($parts[0]);
 }
 private function generateNamespace(ClassMetadata $class)
 {
 $proxyClassName = ClassUtils::generateProxyClassName($class->getName(), $this->proxyNamespace);
 $parts = explode('\\', strrev($proxyClassName), 2);
 return strrev($parts[1]);
 }
 public function generateEnumUseStatements(ClassMetadata $class) : string
 {
 if (PHP_VERSION_ID < 80100) {
 return "\n";
 }
 $defaultProperties = $class->getReflectionClass()->getDefaultProperties();
 $lazyLoadedPublicProperties = $this->getLazyLoadedPublicPropertiesNames($class);
 $enumClasses = [];
 foreach ($class->getReflectionClass()->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
 $name = $property->getName();
 if (!in_array($name, $lazyLoadedPublicProperties, \true)) {
 continue;
 }
 if (array_key_exists($name, $defaultProperties) && $defaultProperties[$name] instanceof BackedEnum) {
 $enumClassNameParts = explode('\\', get_class($defaultProperties[$name]));
 $enumClasses[] = $enumClassNameParts[0];
 }
 }
 return implode("\n", array_map(static function ($className) {
 return 'use ' . $className . ';';
 }, array_unique($enumClasses))) . "\n";
 }
 private function generateClassName(ClassMetadata $class)
 {
 return ltrim($class->getName(), '\\');
 }
 private function generateLazyPropertiesNames(ClassMetadata $class)
 {
 $lazyPublicProperties = $this->getLazyLoadedPublicPropertiesNames($class);
 $values = [];
 foreach ($lazyPublicProperties as $name) {
 $values[$name] = null;
 }
 return var_export($values, \true);
 }
 private function generateLazyPropertiesDefaults(ClassMetadata $class)
 {
 return var_export($this->getLazyLoadedPublicProperties($class), \true);
 }
 private function generateConstructorImpl(ClassMetadata $class)
 {
 $constructorImpl = <<<'EOT'
 public function __construct(?\Closure $initializer = null, ?\Closure $cloner = null)
 {
EOT;
 $toUnset = array_map(static function (string $name) : string {
 return '$this->' . $name;
 }, $this->getLazyLoadedPublicPropertiesNames($class));
 return $constructorImpl . ($toUnset === [] ? '' : ' unset(' . implode(', ', $toUnset) . ");\n") . <<<'EOT'
 $this->__initializer__ = $initializer;
 $this->__cloner__ = $cloner;
 }
EOT;
 }
 private function generateMagicGet(ClassMetadata $class)
 {
 $lazyPublicProperties = $this->getLazyLoadedPublicPropertiesNames($class);
 $reflectionClass = $class->getReflectionClass();
 $hasParentGet = \false;
 $returnReference = '';
 $inheritDoc = '';
 $name = '$name';
 $parametersString = '$name';
 $returnTypeHint = null;
 if ($reflectionClass->hasMethod('__get')) {
 $hasParentGet = \true;
 $inheritDoc = '{@inheritDoc}';
 $methodReflection = $reflectionClass->getMethod('__get');
 if ($methodReflection->returnsReference()) {
 $returnReference = '& ';
 }
 $methodParameters = $methodReflection->getParameters();
 $name = '$' . $methodParameters[0]->getName();
 $parametersString = $this->buildParametersString($methodReflection->getParameters(), ['name']);
 $returnTypeHint = $this->getMethodReturnType($methodReflection);
 }
 if (empty($lazyPublicProperties) && !$hasParentGet) {
 return '';
 }
 $magicGet = <<<EOT
 public function {$returnReference}__get({$parametersString}){$returnTypeHint}
 {
EOT;
 if (!empty($lazyPublicProperties)) {
 $magicGet .= <<<'EOT'
 if (\array_key_exists($name, self::$lazyPropertiesNames)) {
 $this->__initializer__ && $this->__initializer__->__invoke($this, '__get', [$name]);
EOT;
 if ($returnTypeHint === ': void') {
 $magicGet .= "\n return;";
 } else {
 $magicGet .= "\n return \$this->\$name;";
 }
 $magicGet .= <<<'EOT'
 }
EOT;
 }
 if ($hasParentGet) {
 $magicGet .= <<<'EOT'
 $this->__initializer__ && $this->__initializer__->__invoke($this, '__get', [$name]);
EOT;
 if ($returnTypeHint === ': void') {
 $magicGet .= <<<'EOT'
 parent::__get($name);
 return;
EOT;
 } elseif ($returnTypeHint === ': never') {
 $magicGet .= <<<'EOT'
 parent::__get($name);
EOT;
 } else {
 $magicGet .= <<<'EOT'
 return parent::__get($name);
EOT;
 }
 } else {
 $magicGet .= sprintf(<<<EOT
 trigger_error(sprintf('Undefined property: %%s::\$%%s', __CLASS__, %s), E_USER_NOTICE);
EOT
, $name);
 }
 return $magicGet . "\n }";
 }
 private function generateMagicSet(ClassMetadata $class)
 {
 $lazyPublicProperties = $this->getLazyLoadedPublicPropertiesNames($class);
 $reflectionClass = $class->getReflectionClass();
 $hasParentSet = \false;
 $inheritDoc = '';
 $parametersString = '$name, $value';
 $returnTypeHint = null;
 if ($reflectionClass->hasMethod('__set')) {
 $hasParentSet = \true;
 $inheritDoc = '{@inheritDoc}';
 $methodReflection = $reflectionClass->getMethod('__set');
 $parametersString = $this->buildParametersString($methodReflection->getParameters(), ['name', 'value']);
 $returnTypeHint = $this->getMethodReturnType($methodReflection);
 }
 if (empty($lazyPublicProperties) && !$hasParentSet) {
 return '';
 }
 $magicSet = <<<EOT
 public function __set({$parametersString}){$returnTypeHint}
 {
EOT;
 if (!empty($lazyPublicProperties)) {
 $magicSet .= <<<'EOT'
 if (\array_key_exists($name, self::$lazyPropertiesNames)) {
 $this->__initializer__ && $this->__initializer__->__invoke($this, '__set', [$name, $value]);
 $this->$name = $value;
 return;
 }
EOT;
 }
 if ($hasParentSet) {
 $magicSet .= <<<'EOT'
 $this->__initializer__ && $this->__initializer__->__invoke($this, '__set', [$name, $value]);
EOT;
 if ($returnTypeHint === ': void') {
 $magicSet .= <<<'EOT'
 parent::__set($name, $value);
 return;
EOT;
 } elseif ($returnTypeHint === ': never') {
 $magicSet .= <<<'EOT'
 parent::__set($name, $value);
EOT;
 } else {
 $magicSet .= <<<'EOT'
 return parent::__set($name, $value);
EOT;
 }
 } else {
 $magicSet .= ' $this->$name = $value;';
 }
 return $magicSet . "\n }";
 }
 private function generateMagicIsset(ClassMetadata $class)
 {
 $lazyPublicProperties = $this->getLazyLoadedPublicPropertiesNames($class);
 $hasParentIsset = $class->getReflectionClass()->hasMethod('__isset');
 $parametersString = '$name';
 $returnTypeHint = null;
 if ($hasParentIsset) {
 $methodReflection = $class->getReflectionClass()->getMethod('__isset');
 $parametersString = $this->buildParametersString($methodReflection->getParameters(), ['name']);
 $returnTypeHint = $this->getMethodReturnType($methodReflection);
 }
 if (empty($lazyPublicProperties) && !$hasParentIsset) {
 return '';
 }
 $inheritDoc = $hasParentIsset ? '{@inheritDoc}' : '';
 $magicIsset = <<<EOT
 public function __isset({$parametersString}){$returnTypeHint}
 {
EOT;
 if (!empty($lazyPublicProperties)) {
 $magicIsset .= <<<'EOT'
 if (\array_key_exists($name, self::$lazyPropertiesNames)) {
 $this->__initializer__ && $this->__initializer__->__invoke($this, '__isset', [$name]);
 return isset($this->$name);
 }
EOT;
 }
 if ($hasParentIsset) {
 $magicIsset .= <<<'EOT'
 $this->__initializer__ && $this->__initializer__->__invoke($this, '__isset', [$name]);
 return parent::__isset($name);
EOT;
 } else {
 $magicIsset .= ' return false;';
 }
 return $magicIsset . "\n }";
 }
 private function generateSleepImpl(ClassMetadata $class)
 {
 $reflectionClass = $class->getReflectionClass();
 $hasParentSleep = $reflectionClass->hasMethod('__sleep');
 $inheritDoc = $hasParentSleep ? '{@inheritDoc}' : '';
 $returnTypeHint = $hasParentSleep ? $this->getMethodReturnType($reflectionClass->getMethod('__sleep')) : '';
 $sleepImpl = <<<EOT
 public function __sleep(){$returnTypeHint}
 {
EOT;
 if ($hasParentSleep) {
 return $sleepImpl . <<<'EOT'
 $properties = array_merge(['__isInitialized__'], parent::__sleep());
 if ($this->__isInitialized__) {
 $properties = array_diff($properties, array_keys(self::$lazyPropertiesNames));
 }
 return $properties;
 }
EOT;
 }
 $allProperties = ['__isInitialized__'];
 foreach ($class->getReflectionClass()->getProperties() as $prop) {
 assert($prop instanceof ReflectionProperty);
 if ($prop->isStatic()) {
 continue;
 }
 $allProperties[] = $prop->isPrivate() ? "\x00" . $prop->getDeclaringClass()->getName() . "\x00" . $prop->getName() : $prop->getName();
 }
 $lazyPublicProperties = $this->getLazyLoadedPublicPropertiesNames($class);
 $protectedProperties = array_diff($allProperties, $lazyPublicProperties);
 foreach ($allProperties as &$property) {
 $property = var_export($property, \true);
 }
 foreach ($protectedProperties as &$property) {
 $property = var_export($property, \true);
 }
 $allProperties = implode(', ', $allProperties);
 $protectedProperties = implode(', ', $protectedProperties);
 return $sleepImpl . <<<EOT
 if (\$this->__isInitialized__) {
 return [{$allProperties}];
 }
 return [{$protectedProperties}];
 }
EOT;
 }
 private function generateWakeupImpl(ClassMetadata $class)
 {
 $reflectionClass = $class->getReflectionClass();
 $hasParentWakeup = $reflectionClass->hasMethod('__wakeup');
 $unsetPublicProperties = [];
 foreach ($this->getLazyLoadedPublicPropertiesNames($class) as $lazyPublicProperty) {
 $unsetPublicProperties[] = '$this->' . $lazyPublicProperty;
 }
 $shortName = $this->generateProxyShortClassName($class);
 $inheritDoc = $hasParentWakeup ? '{@inheritDoc}' : '';
 $returnTypeHint = $hasParentWakeup ? $this->getMethodReturnType($reflectionClass->getMethod('__wakeup')) : '';
 $wakeupImpl = <<<EOT
 public function __wakeup(){$returnTypeHint}
 {
 if ( ! \$this->__isInitialized__) {
 \$this->__initializer__ = function ({$shortName} \$proxy) {
 \$proxy->__setInitializer(null);
 \$proxy->__setCloner(null);
 \$existingProperties = get_object_vars(\$proxy);
 foreach (\$proxy::\$lazyPropertiesDefaults as \$property => \$defaultValue) {
 if ( ! array_key_exists(\$property, \$existingProperties)) {
 \$proxy->\$property = \$defaultValue;
 }
 }
 };
EOT;
 if (!empty($unsetPublicProperties)) {
 $wakeupImpl .= "\n unset(" . implode(', ', $unsetPublicProperties) . ');';
 }
 $wakeupImpl .= "\n }";
 if ($hasParentWakeup) {
 $wakeupImpl .= "\n parent::__wakeup();";
 }
 $wakeupImpl .= "\n }";
 return $wakeupImpl;
 }
 private function generateCloneImpl(ClassMetadata $class)
 {
 $hasParentClone = $class->getReflectionClass()->hasMethod('__clone');
 $inheritDoc = $hasParentClone ? '{@inheritDoc}' : '';
 $callParentClone = $hasParentClone ? "\n parent::__clone();\n" : '';
 return <<<EOT
 public function __clone()
 {
 \$this->__cloner__ && \$this->__cloner__->__invoke(\$this, '__clone', []);
{$callParentClone} }
EOT;
 }
 private function generateMethods(ClassMetadata $class)
 {
 $methods = '';
 $methodNames = [];
 $reflectionMethods = $class->getReflectionClass()->getMethods(ReflectionMethod::IS_PUBLIC);
 $skippedMethods = ['__sleep' => \true, '__clone' => \true, '__wakeup' => \true, '__get' => \true, '__set' => \true, '__isset' => \true];
 foreach ($reflectionMethods as $method) {
 $name = $method->getName();
 if ($method->isConstructor() || isset($skippedMethods[strtolower($name)]) || isset($methodNames[$name]) || $method->isFinal() || $method->isStatic() || !$method->isPublic()) {
 continue;
 }
 $methodNames[$name] = \true;
 $methods .= "\n /**\n" . " * {@inheritDoc}\n" . " */\n" . ' public function ';
 if ($method->returnsReference()) {
 $methods .= '&';
 }
 $methods .= $name . '(' . $this->buildParametersString($method->getParameters()) . ')';
 $methods .= $this->getMethodReturnType($method);
 $methods .= "\n" . ' {' . "\n";
 if ($this->isShortIdentifierGetter($method, $class)) {
 $identifier = lcfirst(substr($name, 3));
 $fieldType = $class->getTypeOfField($identifier);
 $cast = in_array($fieldType, ['integer', 'smallint']) ? '(int) ' : '';
 $methods .= ' if ($this->__isInitialized__ === false) {' . "\n";
 $methods .= ' ';
 $methods .= $this->shouldProxiedMethodReturn($method) ? 'return ' : '';
 $methods .= $cast . ' parent::' . $method->getName() . "();\n";
 $methods .= ' }' . "\n\n";
 }
 $invokeParamsString = implode(', ', $this->getParameterNamesForInvoke($method->getParameters()));
 $callParamsString = implode(', ', $this->getParameterNamesForParentCall($method->getParameters()));
 $methods .= "\n \$this->__initializer__ " . '&& $this->__initializer__->__invoke($this, ' . var_export($name, \true) . ', [' . $invokeParamsString . ']);' . "\n\n " . ($this->shouldProxiedMethodReturn($method) ? 'return ' : '') . 'parent::' . $name . '(' . $callParamsString . ');' . "\n" . ' }' . "\n";
 }
 return $methods;
 }
 public function getProxyFileName($className, $baseDirectory = null)
 {
 $baseDirectory = $baseDirectory ?: $this->proxyDirectory;
 return rtrim($baseDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . Proxy::MARKER . str_replace('\\', '', $className) . '.php';
 }
 private function isShortIdentifierGetter($method, ClassMetadata $class)
 {
 $identifier = lcfirst(substr($method->getName(), 3));
 $startLine = $method->getStartLine();
 $endLine = $method->getEndLine();
 $cheapCheck = $method->getNumberOfParameters() === 0 && substr($method->getName(), 0, 3) === 'get' && in_array($identifier, $class->getIdentifier(), \true) && $class->hasField($identifier) && $endLine - $startLine <= 4;
 if ($cheapCheck) {
 $code = file($method->getFileName());
 $code = trim(implode(' ', array_slice($code, $startLine - 1, $endLine - $startLine + 1)));
 $pattern = sprintf(self::PATTERN_MATCH_ID_METHOD, $method->getName(), $identifier);
 if (preg_match($pattern, $code)) {
 return \true;
 }
 }
 return \false;
 }
 private function getLazyLoadedPublicPropertiesNames(ClassMetadata $class) : array
 {
 $properties = [];
 foreach ($class->getReflectionClass()->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
 $name = $property->getName();
 if (!$class->hasField($name) && !$class->hasAssociation($name) || $class->isIdentifier($name)) {
 continue;
 }
 $properties[] = $name;
 }
 return $properties;
 }
 private function getLazyLoadedPublicProperties(ClassMetadata $class)
 {
 $defaultProperties = $class->getReflectionClass()->getDefaultProperties();
 $lazyLoadedPublicProperties = $this->getLazyLoadedPublicPropertiesNames($class);
 $defaultValues = [];
 foreach ($class->getReflectionClass()->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
 $name = $property->getName();
 if (!in_array($name, $lazyLoadedPublicProperties, \true)) {
 continue;
 }
 if (array_key_exists($name, $defaultProperties)) {
 $defaultValues[$name] = $defaultProperties[$name];
 } elseif (method_exists($property, 'getType')) {
 $propertyType = $property->getType();
 if ($propertyType !== null && $propertyType->allowsNull()) {
 $defaultValues[$name] = null;
 }
 }
 }
 return $defaultValues;
 }
 private function buildParametersString(array $parameters, array $renameParameters = [])
 {
 $parameterDefinitions = [];
 $i = -1;
 foreach ($parameters as $param) {
 assert($param instanceof ReflectionParameter);
 $i++;
 $parameterDefinition = '';
 $parameterType = $this->getParameterType($param);
 if ($parameterType !== null) {
 $parameterDefinition .= $parameterType . ' ';
 }
 if ($param->isPassedByReference()) {
 $parameterDefinition .= '&';
 }
 if ($param->isVariadic()) {
 $parameterDefinition .= '...';
 }
 $parameterDefinition .= '$' . ($renameParameters ? $renameParameters[$i] : $param->getName());
 if ($param->isDefaultValueAvailable()) {
 $parameterDefinition .= ' = ' . var_export($param->getDefaultValue(), \true);
 }
 $parameterDefinitions[] = $parameterDefinition;
 }
 return implode(', ', $parameterDefinitions);
 }
 private function getParameterType(ReflectionParameter $parameter)
 {
 if (!$parameter->hasType()) {
 return null;
 }
 $declaringFunction = $parameter->getDeclaringFunction();
 assert($declaringFunction instanceof ReflectionMethod);
 return $this->formatType($parameter->getType(), $declaringFunction, $parameter);
 }
 private function getParameterNamesForInvoke(array $parameters)
 {
 return array_map(static function (ReflectionParameter $parameter) {
 return '$' . $parameter->getName();
 }, $parameters);
 }
 private function getParameterNamesForParentCall(array $parameters)
 {
 return array_map(static function (ReflectionParameter $parameter) {
 $name = '';
 if ($parameter->isVariadic()) {
 $name .= '...';
 }
 $name .= '$' . $parameter->getName();
 return $name;
 }, $parameters);
 }
 private function getMethodReturnType(ReflectionMethod $method)
 {
 if (!$method->hasReturnType()) {
 return '';
 }
 return ': ' . $this->formatType($method->getReturnType(), $method);
 }
 private function shouldProxiedMethodReturn(ReflectionMethod $method)
 {
 if (!$method->hasReturnType()) {
 return \true;
 }
 return !in_array(strtolower($this->formatType($method->getReturnType(), $method)), ['void', 'never'], \true);
 }
 private function formatType(ReflectionType $type, ReflectionMethod $method, ?ReflectionParameter $parameter = null)
 {
 if ($type instanceof ReflectionUnionType) {
 return implode('|', array_map(function (ReflectionType $unionedType) use($method, $parameter) {
 return $this->formatType($unionedType, $method, $parameter);
 }, $type->getTypes()));
 }
 if ($type instanceof ReflectionIntersectionType) {
 return implode('&', array_map(function (ReflectionType $intersectedType) use($method, $parameter) {
 return $this->formatType($intersectedType, $method, $parameter);
 }, $type->getTypes()));
 }
 assert($type instanceof ReflectionNamedType);
 $name = $type->getName();
 $nameLower = strtolower($name);
 if ($nameLower === 'static') {
 $name = 'static';
 }
 if ($nameLower === 'self') {
 $name = $method->getDeclaringClass()->getName();
 }
 if ($nameLower === 'parent') {
 $name = $method->getDeclaringClass()->getParentClass()->getName();
 }
 if (!$type->isBuiltin() && !class_exists($name) && !interface_exists($name) && $name !== 'static') {
 if ($parameter !== null) {
 throw UnexpectedValueException::invalidParameterTypeHint($method->getDeclaringClass()->getName(), $method->getName(), $parameter->getName());
 }
 throw UnexpectedValueException::invalidReturnTypeHint($method->getDeclaringClass()->getName(), $method->getName());
 }
 if (!$type->isBuiltin() && $name !== 'static') {
 $name = '\\' . $name;
 }
 if ($type->allowsNull() && !in_array($name, ['mixed', 'null'], \true) && ($parameter === null || !$parameter->isDefaultValueAvailable() || $parameter->getDefaultValue() !== null)) {
 $name = '?' . $name;
 }
 return $name;
 }
}
