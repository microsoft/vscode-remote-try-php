<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Id;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
class BigIntegerIdentityGenerator extends AbstractIdGenerator
{
 private $sequenceName;
 public function __construct($sequenceName = null)
 {
 if ($sequenceName !== null) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8850', 'Passing a sequence name to the IdentityGenerator is deprecated in favor of using %s. $sequenceName will be removed in ORM 3.0', SequenceGenerator::class);
 }
 $this->sequenceName = $sequenceName;
 }
 public function generateId(EntityManagerInterface $em, $entity)
 {
 return (string) $em->getConnection()->lastInsertId($this->sequenceName);
 }
 public function isPostInsertGenerator()
 {
 return \true;
 }
}
