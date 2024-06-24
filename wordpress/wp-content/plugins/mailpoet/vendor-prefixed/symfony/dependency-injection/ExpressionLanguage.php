<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use MailPoetVendor\Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;
if (!\class_exists(BaseExpressionLanguage::class)) {
 return;
}
class ExpressionLanguage extends BaseExpressionLanguage
{
 public function __construct(?CacheItemPoolInterface $cache = null, array $providers = [], ?callable $serviceCompiler = null)
 {
 // prepend the default provider to let users override it easily
 \array_unshift($providers, new ExpressionLanguageProvider($serviceCompiler));
 parent::__construct($cache, $providers);
 }
}
