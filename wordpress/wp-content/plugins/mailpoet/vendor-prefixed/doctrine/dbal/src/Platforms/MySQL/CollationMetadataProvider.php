<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Platforms\MySQL;
if (!defined('ABSPATH')) exit;
interface CollationMetadataProvider
{
 public function getCollationCharset(string $collation) : ?string;
}
