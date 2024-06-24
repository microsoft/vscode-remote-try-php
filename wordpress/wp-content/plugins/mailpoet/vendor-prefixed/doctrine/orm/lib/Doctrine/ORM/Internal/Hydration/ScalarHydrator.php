<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal\Hydration;
if (!defined('ABSPATH')) exit;
class ScalarHydrator extends AbstractHydrator
{
 protected function hydrateAllData()
 {
 $result = [];
 while ($data = $this->statement()->fetchAssociative()) {
 $this->hydrateRowData($data, $result);
 }
 return $result;
 }
 protected function hydrateRowData(array $row, array &$result)
 {
 $result[] = $this->gatherScalarRowData($row);
 }
}
