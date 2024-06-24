<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal\Hydration;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use MailPoetVendor\Doctrine\ORM\Exception\MultipleSelectorsFoundException;
use function array_column;
use function count;
final class ScalarColumnHydrator extends AbstractHydrator
{
 protected function hydrateAllData() : array
 {
 if (count($this->resultSetMapping()->fieldMappings) > 1) {
 throw MultipleSelectorsFoundException::create($this->resultSetMapping()->fieldMappings);
 }
 $result = $this->statement()->fetchAllNumeric();
 return array_column($result, 0);
 }
}
