<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Attribute;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class Target
{
 public $name;
 public function __construct(string $name)
 {
 $this->name = \lcfirst(\str_replace(' ', '', \ucwords(\preg_replace('/[^a-zA-Z0-9\\x7f-\\xff]++/', ' ', $name))));
 }
 public static function parseName(\ReflectionParameter $parameter) : string
 {
 if (80000 > \PHP_VERSION_ID || !($target = $parameter->getAttributes(self::class)[0] ?? null)) {
 return $parameter->name;
 }
 $name = $target->newInstance()->name;
 if (!\preg_match('/^[a-zA-Z_\\x7f-\\xff]/', $name)) {
 if (($function = $parameter->getDeclaringFunction()) instanceof \ReflectionMethod) {
 $function = $function->class . '::' . $function->name;
 } else {
 $function = $function->name;
 }
 throw new InvalidArgumentException(\sprintf('Invalid #[Target] name "%s" on parameter "$%s" of "%s()": the first character must be a letter.', $name, $parameter->name, $function));
 }
 return $name;
 }
}
