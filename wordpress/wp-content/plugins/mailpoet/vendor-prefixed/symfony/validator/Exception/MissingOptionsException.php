<?php
namespace MailPoetVendor\Symfony\Component\Validator\Exception;
if (!defined('ABSPATH')) exit;
class MissingOptionsException extends ValidatorException
{
 private $options;
 public function __construct(string $message, array $options)
 {
 parent::__construct($message);
 $this->options = $options;
 }
 public function getOptions()
 {
 return $this->options;
 }
}
