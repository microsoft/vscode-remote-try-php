<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use MailPoetVendor\Symfony\Component\ExpressionLanguage\SyntaxError;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class ExpressionLanguageSyntaxValidator extends ConstraintValidator
{
 private $expressionLanguage;
 public function __construct(ExpressionLanguage $expressionLanguage = null)
 {
 $this->expressionLanguage = $expressionLanguage;
 }
 public function validate($expression, Constraint $constraint) : void
 {
 if (!$constraint instanceof ExpressionLanguageSyntax) {
 throw new UnexpectedTypeException($constraint, ExpressionLanguageSyntax::class);
 }
 if (!\is_string($expression)) {
 throw new UnexpectedValueException($expression, 'string');
 }
 if (null === $this->expressionLanguage) {
 $this->expressionLanguage = new ExpressionLanguage();
 }
 try {
 $this->expressionLanguage->lint($expression, $constraint->allowedVariables);
 } catch (SyntaxError $exception) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ syntax_error }}', $this->formatValue($exception->getMessage()))->setInvalidValue((string) $expression)->setCode(ExpressionLanguageSyntax::EXPRESSION_LANGUAGE_SYNTAX_ERROR)->addViolation();
 }
 }
}
