<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Abstraction;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Result as DriverResult;
use MailPoetVendor\Doctrine\DBAL\Exception;
use Traversable;
interface Result extends DriverResult
{
 public function iterateNumeric() : Traversable;
 public function iterateAssociative() : Traversable;
 public function iterateColumn() : Traversable;
}
