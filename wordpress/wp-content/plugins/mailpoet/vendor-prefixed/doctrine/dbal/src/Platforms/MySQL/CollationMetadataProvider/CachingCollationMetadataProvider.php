<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Platforms\MySQL\CollationMetadataProvider;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\MySQL\CollationMetadataProvider;
use function array_key_exists;
final class CachingCollationMetadataProvider implements CollationMetadataProvider
{
 private $collationMetadataProvider;
 private $cache = [];
 public function __construct(CollationMetadataProvider $collationMetadataProvider)
 {
 $this->collationMetadataProvider = $collationMetadataProvider;
 }
 public function getCollationCharset(string $collation) : ?string
 {
 if (array_key_exists($collation, $this->cache)) {
 return $this->cache[$collation];
 }
 return $this->cache[$collation] = $this->collationMetadataProvider->getCollationCharset($collation);
 }
}
