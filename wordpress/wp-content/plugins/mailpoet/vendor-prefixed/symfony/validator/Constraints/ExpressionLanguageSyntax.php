<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ExpressionLanguageSyntax extends Constraint
{
 public const EXPRESSION_LANGUAGE_SYNTAX_ERROR = '1766a3f3-ff03-40eb-b053-ab7aa23d988a';
 protected static $errorNames = [self::EXPRESSION_LANGUAGE_SYNTAX_ERROR => 'EXPRESSION_LANGUAGE_SYNTAX_ERROR'];
 public $message = 'This value should be a valid expression.';
 public $service;
 public $allowedVariables;
 public function __construct(array $options = null, string $message = null, string $service = null, array $allowedVariables = null, array $groups = null, $payload = null)
 {
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->service = $service ?? $this->service;
 $this->allowedVariables = $allowedVariables ?? $this->allowedVariables;
 }
 public function validatedBy()
 {
 return $this->service ?? static::class . 'Validator';
 }
}
