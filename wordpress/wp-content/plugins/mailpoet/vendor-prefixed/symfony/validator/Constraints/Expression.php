<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\ExpressionLanguage\Expression as ExpressionObject;
use MailPoetVendor\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Expression extends Constraint
{
 public const EXPRESSION_FAILED_ERROR = '6b3befbc-2f01-4ddf-be21-b57898905284';
 protected static $errorNames = [self::EXPRESSION_FAILED_ERROR => 'EXPRESSION_FAILED_ERROR'];
 public $message = 'This value is not valid.';
 public $expression;
 public $values = [];
 public function __construct($expression, string $message = null, array $values = null, array $groups = null, $payload = null, array $options = [])
 {
 if (!\class_exists(ExpressionLanguage::class)) {
 throw new LogicException(\sprintf('The "symfony/expression-language" component is required to use the "%s" constraint.', __CLASS__));
 }
 if (\is_array($expression)) {
 $options = \array_merge($expression, $options);
 } elseif (!\is_string($expression) && !$expression instanceof ExpressionObject) {
 throw new \TypeError(\sprintf('"%s": Expected argument $expression to be either a string, an instance of "%s" or an array, got "%s".', __METHOD__, ExpressionObject::class, \get_debug_type($expression)));
 } else {
 $options['value'] = $expression;
 }
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->values = $values ?? $this->values;
 }
 public function getDefaultOption()
 {
 return 'expression';
 }
 public function getRequiredOptions()
 {
 return ['expression'];
 }
 public function getTargets()
 {
 return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
 }
 public function validatedBy()
 {
 return 'validator.expression';
 }
}
