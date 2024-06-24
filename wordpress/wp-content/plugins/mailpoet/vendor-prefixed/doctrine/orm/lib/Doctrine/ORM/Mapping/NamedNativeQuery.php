<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
final class NamedNativeQuery implements MappingAttribute
{
 public $name;
 public $query;
 public $resultClass;
 public $resultSetMapping;
}
