<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Exception;
if (!defined('ABSPATH')) exit;
class ParameterCircularReferenceException extends RuntimeException
{
 private $parameters;
 public function __construct(array $parameters, ?\Throwable $previous = null)
 {
 parent::__construct(\sprintf('Circular reference detected for parameter "%s" ("%s" > "%s").', $parameters[0], \implode('" > "', $parameters), $parameters[0]), 0, $previous);
 $this->parameters = $parameters;
 }
 public function getParameters()
 {
 return $this->parameters;
 }
}
