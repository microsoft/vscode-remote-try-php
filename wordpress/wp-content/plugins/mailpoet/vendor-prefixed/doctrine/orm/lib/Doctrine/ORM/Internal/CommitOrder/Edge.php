<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal\CommitOrder;
if (!defined('ABSPATH')) exit;
final class Edge
{
 public $from;
 public $to;
 public $weight;
 public function __construct(string $from, string $to, int $weight)
 {
 $this->from = $from;
 $this->to = $to;
 $this->weight = $weight;
 }
}
