<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
interface ConstraintViolationInterface
{
 public function getMessage();
 public function getMessageTemplate();
 public function getParameters();
 public function getPlural();
 public function getRoot();
 public function getPropertyPath();
 public function getInvalidValue();
 public function getCode();
}
