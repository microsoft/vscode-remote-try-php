<?php
namespace MailPoetVendor\Symfony\Component\Validator\Context;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface;
interface ExecutionContextFactoryInterface
{
 public function createContext(ValidatorInterface $validator, $root);
}
