<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class ExpressionValidator extends ConstraintValidator
{
 private $expressionLanguage;
 public function __construct(ExpressionLanguage $expressionLanguage = null)
 {
 $this->expressionLanguage = $expressionLanguage;
 }
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Expression) {
 throw new UnexpectedTypeException($constraint, Expression::class);
 }
 $variables = $constraint->values;
 $variables['value'] = $value;
 $variables['this'] = $this->context->getObject();
 if (!$this->getExpressionLanguage()->evaluate($constraint->expression, $variables)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value, self::OBJECT_TO_STRING))->setCode(Expression::EXPRESSION_FAILED_ERROR)->addViolation();
 }
 }
 private function getExpressionLanguage() : ExpressionLanguage
 {
 if (null === $this->expressionLanguage) {
 $this->expressionLanguage = new ExpressionLanguage();
 }
 return $this->expressionLanguage;
 }
}
