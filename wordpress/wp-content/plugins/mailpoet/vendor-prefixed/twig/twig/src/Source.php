<?php
namespace MailPoetVendor\Twig;
if (!defined('ABSPATH')) exit;
final class Source
{
 private $code;
 private $name;
 private $path;
 public function __construct(string $code, string $name, string $path = '')
 {
 $this->code = $code;
 $this->name = $name;
 $this->path = $path;
 }
 public function getCode() : string
 {
 return $this->code;
 }
 public function getName() : string
 {
 return $this->name;
 }
 public function getPath() : string
 {
 return $this->path;
 }
}
