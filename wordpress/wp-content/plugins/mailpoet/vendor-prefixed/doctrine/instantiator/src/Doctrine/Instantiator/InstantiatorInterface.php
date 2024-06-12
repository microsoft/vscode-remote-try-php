<?php
namespace MailPoetVendor\Doctrine\Instantiator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Instantiator\Exception\ExceptionInterface;
interface InstantiatorInterface
{
 public function instantiate($className);
}
