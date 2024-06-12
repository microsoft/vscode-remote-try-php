<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal\Hydration;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\NonUniqueResultException;
use MailPoetVendor\Doctrine\ORM\NoResultException;
use function array_shift;
use function count;
use function key;
class SingleScalarHydrator extends AbstractHydrator
{
 protected function hydrateAllData()
 {
 $data = $this->statement()->fetchAllAssociative();
 $numRows = count($data);
 if ($numRows === 0) {
 throw new NoResultException();
 }
 if ($numRows > 1) {
 throw new NonUniqueResultException('The query returned multiple rows. Change the query or use a different result function like getScalarResult().');
 }
 $result = $this->gatherScalarRowData($data[key($data)]);
 if (count($result) > 1) {
 throw new NonUniqueResultException('The query returned a row containing multiple columns. Change the query or use a different result function like getScalarResult().');
 }
 return array_shift($result);
 }
}
