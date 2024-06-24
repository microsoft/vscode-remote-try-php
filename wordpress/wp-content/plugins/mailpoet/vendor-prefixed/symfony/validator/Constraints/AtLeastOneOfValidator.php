<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class AtLeastOneOfValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof AtLeastOneOf) {
 throw new UnexpectedTypeException($constraint, AtLeastOneOf::class);
 }
 $validator = $this->context->getValidator();
 // Build a first violation to have the base message of the constraint translated
 $baseMessageContext = clone $this->context;
 $baseMessageContext->buildViolation($constraint->message)->addViolation();
 $baseViolations = $baseMessageContext->getViolations();
 $messages = [(string) $baseViolations->get(\count($baseViolations) - 1)->getMessage()];
 foreach ($constraint->constraints as $key => $item) {
 if (!\in_array($this->context->getGroup(), $item->groups, \true)) {
 continue;
 }
 $executionContext = clone $this->context;
 $executionContext->setNode($value, $this->context->getObject(), $this->context->getMetadata(), $this->context->getPropertyPath());
 $violations = $validator->inContext($executionContext)->validate($value, $item, $this->context->getGroup())->getViolations();
 if (\count($this->context->getViolations()) === \count($violations)) {
 return;
 }
 if ($constraint->includeInternalMessages) {
 $message = ' [' . ($key + 1) . '] ';
 if ($item instanceof All || $item instanceof Collection) {
 $message .= $constraint->messageCollection;
 } else {
 $message .= $violations->get(\count($violations) - 1)->getMessage();
 }
 $messages[] = $message;
 }
 }
 $this->context->buildViolation(\implode('', $messages))->setCode(AtLeastOneOf::AT_LEAST_ONE_OF_ERROR)->addViolation();
 }
}
