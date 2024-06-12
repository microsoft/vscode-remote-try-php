<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
final class AssociationOverride implements Annotation
{
 public $name;
 public $joinColumns;
 public $inverseJoinColumns;
 public $joinTable;
 public $inversedBy;
 public $fetch;
 public function __construct(string $name, $joinColumns = null, $inverseJoinColumns = null, ?JoinTable $joinTable = null, ?string $inversedBy = null, ?string $fetch = null)
 {
 if ($joinColumns instanceof JoinColumn) {
 $joinColumns = [$joinColumns];
 }
 if ($inverseJoinColumns instanceof JoinColumn) {
 $inverseJoinColumns = [$inverseJoinColumns];
 }
 $this->name = $name;
 $this->joinColumns = $joinColumns;
 $this->inverseJoinColumns = $inverseJoinColumns;
 $this->joinTable = $joinTable;
 $this->inversedBy = $inversedBy;
 $this->fetch = $fetch;
 }
}
