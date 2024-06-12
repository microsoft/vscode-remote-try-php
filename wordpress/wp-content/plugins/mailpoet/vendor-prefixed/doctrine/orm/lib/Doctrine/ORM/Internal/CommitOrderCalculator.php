<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal;
if (!defined('ABSPATH')) exit;
use stdClass;
use function array_reverse;
class CommitOrderCalculator
{
 public const NOT_VISITED = 0;
 public const IN_PROGRESS = 1;
 public const VISITED = 2;
 private $nodeList = [];
 private $sortedNodeList = [];
 public function hasNode($hash)
 {
 return isset($this->nodeList[$hash]);
 }
 public function addNode($hash, $node)
 {
 $vertex = new stdClass();
 $vertex->hash = $hash;
 $vertex->state = self::NOT_VISITED;
 $vertex->value = $node;
 $vertex->dependencyList = [];
 $this->nodeList[$hash] = $vertex;
 }
 public function addDependency($fromHash, $toHash, $weight)
 {
 $vertex = $this->nodeList[$fromHash];
 $edge = new stdClass();
 $edge->from = $fromHash;
 $edge->to = $toHash;
 $edge->weight = $weight;
 $vertex->dependencyList[$toHash] = $edge;
 }
 public function sort()
 {
 foreach ($this->nodeList as $vertex) {
 if ($vertex->state !== self::NOT_VISITED) {
 continue;
 }
 $this->visit($vertex);
 }
 $sortedList = $this->sortedNodeList;
 $this->nodeList = [];
 $this->sortedNodeList = [];
 return array_reverse($sortedList);
 }
 private function visit(stdClass $vertex) : void
 {
 $vertex->state = self::IN_PROGRESS;
 foreach ($vertex->dependencyList as $edge) {
 $adjacentVertex = $this->nodeList[$edge->to];
 switch ($adjacentVertex->state) {
 case self::VISITED:
 // Do nothing, since node was already visited
 break;
 case self::IN_PROGRESS:
 if (isset($adjacentVertex->dependencyList[$vertex->hash]) && $adjacentVertex->dependencyList[$vertex->hash]->weight < $edge->weight) {
 // If we have some non-visited dependencies in the in-progress dependency, we
 // need to visit them before adding the node.
 foreach ($adjacentVertex->dependencyList as $adjacentEdge) {
 $adjacentEdgeVertex = $this->nodeList[$adjacentEdge->to];
 if ($adjacentEdgeVertex->state === self::NOT_VISITED) {
 $this->visit($adjacentEdgeVertex);
 }
 }
 $adjacentVertex->state = self::VISITED;
 $this->sortedNodeList[] = $adjacentVertex->value;
 }
 break;
 case self::NOT_VISITED:
 $this->visit($adjacentVertex);
 }
 }
 if ($vertex->state !== self::VISITED) {
 $vertex->state = self::VISITED;
 $this->sortedNodeList[] = $vertex->value;
 }
 }
}
