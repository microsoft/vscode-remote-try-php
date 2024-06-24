<?php
namespace MailPoetVendor\Doctrine\Common\Proxy;
if (!defined('ABSPATH')) exit;
use Closure;
use MailPoetVendor\Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use function call_user_func;
use function file_exists;
use function is_callable;
use function ltrim;
use function spl_autoload_register;
use function str_replace;
use function strlen;
use function strpos;
use function substr;
use const DIRECTORY_SEPARATOR;
class Autoloader
{
 public static function resolveFile($proxyDir, $proxyNamespace, $className)
 {
 if (strpos($className, $proxyNamespace) !== 0) {
 throw InvalidArgumentException::notProxyClass($className, $proxyNamespace);
 }
 // remove proxy namespace from class name
 $classNameRelativeToProxyNamespace = substr($className, strlen($proxyNamespace));
 // remove namespace separators from remaining class name
 $fileName = str_replace('\\', '', $classNameRelativeToProxyNamespace);
 return $proxyDir . DIRECTORY_SEPARATOR . $fileName . '.php';
 }
 public static function register($proxyDir, $proxyNamespace, $notFoundCallback = null)
 {
 $proxyNamespace = ltrim($proxyNamespace, '\\');
 if ($notFoundCallback !== null && !is_callable($notFoundCallback)) {
 throw InvalidArgumentException::invalidClassNotFoundCallback($notFoundCallback);
 }
 $autoloader = static function ($className) use($proxyDir, $proxyNamespace, $notFoundCallback) {
 if ($proxyNamespace === '') {
 return;
 }
 if (strpos($className, $proxyNamespace) !== 0) {
 return;
 }
 $file = Autoloader::resolveFile($proxyDir, $proxyNamespace, $className);
 if ($notFoundCallback && !file_exists($file)) {
 call_user_func($notFoundCallback, $proxyDir, $proxyNamespace, $className);
 }
 require $file;
 };
 spl_autoload_register($autoloader);
 return $autoloader;
 }
}
