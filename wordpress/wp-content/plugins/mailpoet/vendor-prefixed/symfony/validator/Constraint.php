<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use MailPoetVendor\Symfony\Component\Validator\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\Validator\Exception\InvalidOptionsException;
use MailPoetVendor\Symfony\Component\Validator\Exception\MissingOptionsException;
abstract class Constraint
{
 public const DEFAULT_GROUP = 'Default';
 public const CLASS_CONSTRAINT = 'class';
 public const PROPERTY_CONSTRAINT = 'property';
 protected static $errorNames = [];
 public $payload;
 public $groups;
 public static function getErrorName(string $errorCode)
 {
 if (!isset(static::$errorNames[$errorCode])) {
 throw new InvalidArgumentException(\sprintf('The error code "%s" does not exist for constraint of type "%s".', $errorCode, static::class));
 }
 return static::$errorNames[$errorCode];
 }
 public function __construct($options = null, array $groups = null, $payload = null)
 {
 unset($this->groups);
 // enable lazy initialization
 $options = $this->normalizeOptions($options);
 if (null !== $groups) {
 $options['groups'] = $groups;
 }
 $options['payload'] = $payload ?? $options['payload'] ?? null;
 foreach ($options as $name => $value) {
 $this->{$name} = $value;
 }
 }
 protected function normalizeOptions($options) : array
 {
 $normalizedOptions = [];
 $defaultOption = $this->getDefaultOption();
 $invalidOptions = [];
 $missingOptions = \array_flip((array) $this->getRequiredOptions());
 $knownOptions = \get_class_vars(static::class);
 if (\is_array($options) && isset($options['value']) && !\property_exists($this, 'value')) {
 if (null === $defaultOption) {
 throw new ConstraintDefinitionException(\sprintf('No default option is configured for constraint "%s".', static::class));
 }
 $options[$defaultOption] = $options['value'];
 unset($options['value']);
 }
 if (\is_array($options)) {
 \reset($options);
 }
 if ($options && \is_array($options) && \is_string(\key($options))) {
 foreach ($options as $option => $value) {
 if (\array_key_exists($option, $knownOptions)) {
 $normalizedOptions[$option] = $value;
 unset($missingOptions[$option]);
 } else {
 $invalidOptions[] = $option;
 }
 }
 } elseif (null !== $options && !(\is_array($options) && 0 === \count($options))) {
 if (null === $defaultOption) {
 throw new ConstraintDefinitionException(\sprintf('No default option is configured for constraint "%s".', static::class));
 }
 if (\array_key_exists($defaultOption, $knownOptions)) {
 $normalizedOptions[$defaultOption] = $options;
 unset($missingOptions[$defaultOption]);
 } else {
 $invalidOptions[] = $defaultOption;
 }
 }
 if (\count($invalidOptions) > 0) {
 throw new InvalidOptionsException(\sprintf('The options "%s" do not exist in constraint "%s".', \implode('", "', $invalidOptions), static::class), $invalidOptions);
 }
 if (\count($missingOptions) > 0) {
 throw new MissingOptionsException(\sprintf('The options "%s" must be set for constraint "%s".', \implode('", "', \array_keys($missingOptions)), static::class), \array_keys($missingOptions));
 }
 return $normalizedOptions;
 }
 public function __set(string $option, $value)
 {
 if ('groups' === $option) {
 $this->groups = (array) $value;
 return;
 }
 throw new InvalidOptionsException(\sprintf('The option "%s" does not exist in constraint "%s".', $option, static::class), [$option]);
 }
 public function __get(string $option)
 {
 if ('groups' === $option) {
 $this->groups = [self::DEFAULT_GROUP];
 return $this->groups;
 }
 throw new InvalidOptionsException(\sprintf('The option "%s" does not exist in constraint "%s".', $option, static::class), [$option]);
 }
 public function __isset(string $option)
 {
 return 'groups' === $option;
 }
 public function addImplicitGroupName(string $group)
 {
 if (null === $this->groups && \array_key_exists('groups', (array) $this)) {
 throw new \LogicException(\sprintf('"%s::$groups" is set to null. Did you forget to call "%s::__construct()"?', static::class, self::class));
 }
 if (\in_array(self::DEFAULT_GROUP, $this->groups) && !\in_array($group, $this->groups)) {
 $this->groups[] = $group;
 }
 }
 public function getDefaultOption()
 {
 return null;
 }
 public function getRequiredOptions()
 {
 return [];
 }
 public function validatedBy()
 {
 return static::class . 'Validator';
 }
 public function getTargets()
 {
 return self::PROPERTY_CONSTRAINT;
 }
 public function __sleep() : array
 {
 // Initialize "groups" option if it is not set
 $this->groups;
 return \array_keys(\get_object_vars($this));
 }
}
