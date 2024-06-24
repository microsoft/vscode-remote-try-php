<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal\CommitOrder;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
final class Vertex
{
 public $hash;
 public $state = VertexState::NOT_VISITED;
 public $value;
 public $dependencyList = [];
 public function __construct(string $hash, ClassMetadata $value)
 {
 $this->hash = $hash;
 $this->value = $value;
 }
}
