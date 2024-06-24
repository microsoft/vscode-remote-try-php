<?php
namespace MailPoetVendor\Carbon;
if (!defined('ABSPATH')) exit;
use ReflectionMethod;
use MailPoetVendor\Symfony\Component\Translation;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface;
$transMethod = new ReflectionMethod(\class_exists(TranslatorInterface::class) ? TranslatorInterface::class : Translation\Translator::class, 'trans');
require $transMethod->hasReturnType() ? __DIR__ . '/../../lazy/Carbon/TranslatorStrongType.php' : __DIR__ . '/../../lazy/Carbon/TranslatorWeakType.php';
class Translator extends LazyTranslator
{
 // Proxy dynamically loaded LazyTranslator in a static way
}
