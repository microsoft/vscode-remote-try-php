<?php
namespace MailPoetVendor\Doctrine\Common\Proxy;
if (!defined('ABSPATH')) exit;
use Closure;
use MailPoetVendor\Doctrine\Persistence\Proxy as BaseProxy;
interface Proxy extends BaseProxy
{
 public function __setInitialized($initialized);
 public function __setInitializer(?Closure $initializer = null);
 public function __getInitializer();
 public function __setCloner(?Closure $cloner = null);
 public function __getCloner();
 public function __getLazyProperties();
}
