<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
use function sprintf;
class DB2111Platform extends DB2Platform
{
 protected function doModifyLimitQuery($query, $limit, $offset)
 {
 if ($offset > 0) {
 $query .= sprintf(' OFFSET %u ROWS', $offset);
 }
 if ($limit !== null) {
 if ($limit < 0) {
 throw new Exception(sprintf('Limit must be a positive integer or zero, %d given', $limit));
 }
 $query .= sprintf(' FETCH %s %u ROWS ONLY', $offset === 0 ? 'FIRST' : 'NEXT', $limit);
 }
 return $query;
 }
}
