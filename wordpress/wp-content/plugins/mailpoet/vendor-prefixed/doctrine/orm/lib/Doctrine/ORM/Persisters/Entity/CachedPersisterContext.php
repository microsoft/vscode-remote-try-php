<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Persisters\Entity;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
class CachedPersisterContext
{
 public $class;
 public $rsm;
 public $selectColumnListSql;
 public $selectJoinSql;
 public $sqlAliasCounter = 0;
 public $sqlTableAliases = [];
 public $handlesLimits;
 public function __construct(ClassMetadata $class, ResultSetMapping $rsm, $handlesLimits)
 {
 $this->class = $class;
 $this->rsm = $rsm;
 $this->handlesLimits = (bool) $handlesLimits;
 }
}
