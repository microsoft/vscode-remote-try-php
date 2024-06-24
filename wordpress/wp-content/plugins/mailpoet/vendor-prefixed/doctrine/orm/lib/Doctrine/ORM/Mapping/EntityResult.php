<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
final class EntityResult implements MappingAttribute
{
 public $entityClass;
 public $fields = [];
 public $discriminatorColumn;
}
