<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Exception;
if (!defined('ABSPATH')) exit;
use function sprintf;
class CannotUpdateReadOnlyCollection extends CacheException
{
 public static function fromEntityAndField(string $sourceEntity, string $fieldName) : self
 {
 return new self(sprintf('Cannot update a readonly collection "%s#%s"', $sourceEntity, $fieldName));
 }
}
