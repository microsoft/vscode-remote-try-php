<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\Persistence\Mapping\Driver\FileLocator;
use MailPoetVendor\Doctrine\Persistence\Mapping\Driver\PHPDriver as CommonPHPDriver;
class PHPDriver extends CommonPHPDriver
{
 public function __construct($locator)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/9277', 'PHPDriver is deprecated, use StaticPHPDriver or other mapping drivers instead.');
 parent::__construct($locator);
 }
}
