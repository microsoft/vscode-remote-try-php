<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
abstract class CacheKey
{
 public $hash;
 public function __construct(?string $hash = null)
 {
 if ($hash === null) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/10212', 'Calling %s() without providing a value for the $hash parameter is deprecated.', __METHOD__);
 } else {
 $this->hash = $hash;
 }
 }
}
