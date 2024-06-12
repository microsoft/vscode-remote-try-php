<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
interface ObjectInitializerInterface
{
 public function initialize(object $object);
}
