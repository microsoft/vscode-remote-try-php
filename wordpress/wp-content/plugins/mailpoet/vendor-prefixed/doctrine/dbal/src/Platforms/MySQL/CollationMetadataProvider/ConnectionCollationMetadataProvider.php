<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Platforms\MySQL\CollationMetadataProvider;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\Platforms\MySQL\CollationMetadataProvider;
final class ConnectionCollationMetadataProvider implements CollationMetadataProvider
{
 private $connection;
 public function __construct(Connection $connection)
 {
 $this->connection = $connection;
 }
 public function getCollationCharset(string $collation) : ?string
 {
 $charset = $this->connection->fetchOne(<<<'SQL'
SELECT CHARACTER_SET_NAME
FROM information_schema.COLLATIONS
WHERE COLLATION_NAME = ?;
SQL
, [$collation]);
 if ($charset !== \false) {
 return $charset;
 }
 return null;
 }
}
