<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use MailPoetVendor\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
 private $serviceCompiler;
 public function __construct(callable $serviceCompiler = null)
 {
 $this->serviceCompiler = $serviceCompiler;
 }
 public function getFunctions()
 {
 return [new ExpressionFunction('service', $this->serviceCompiler ?: function ($arg) {
 return \sprintf('$this->get(%s)', $arg);
 }, function (array $variables, $value) {
 return $variables['container']->get($value);
 }), new ExpressionFunction('parameter', function ($arg) {
 return \sprintf('$this->getParameter(%s)', $arg);
 }, function (array $variables, $value) {
 return $variables['container']->getParameter($value);
 })];
 }
}
