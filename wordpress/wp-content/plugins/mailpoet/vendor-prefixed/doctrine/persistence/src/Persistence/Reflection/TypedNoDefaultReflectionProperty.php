<?php
namespace MailPoetVendor\Doctrine\Persistence\Reflection;
if (!defined('ABSPATH')) exit;
use ReflectionProperty;
class TypedNoDefaultReflectionProperty extends ReflectionProperty
{
 use TypedNoDefaultReflectionPropertyBase;
}
