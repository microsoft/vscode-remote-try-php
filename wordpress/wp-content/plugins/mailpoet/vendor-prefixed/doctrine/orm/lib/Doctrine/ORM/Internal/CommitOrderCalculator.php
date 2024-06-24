<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Internal\CommitOrder\Edge;
use MailPoetVendor\Doctrine\ORM\Internal\CommitOrder\Vertex;
use MailPoetVendor\Doctrine\ORM\Internal\CommitOrder\VertexState;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use function array_reverse;
class CommitOrderCalculator
{
 public const NOT_VISITED = VertexState::NOT_VISITED;
 public const IN_PROGRESS = VertexState::IN_PROGRESS;
 public const VISITED = VertexState::VISITED;
 private $nodeList = [];
 private $sortedNodeList = [];
 public function hasNode($hash)
 {
 return isset($this->nodeList[$hash]);
 }
 public function addNode($hash, $node)
 {
 $this->nodeList[$hash] = new Vertex($hash, $node);
 }
 public function addDependency($fromHash, $toHash, $weight)
 {
 $this->nodeList[$fromHash]->dependencyList[$toHash] = new Edge($fromHash, $toHash, $weight);
 }
 public function sort()
 {
 foreach ($this->nodeList as $vertex) {
 if ($vertex->state !== VertexState::NOT_VISITED) {
 continue;
 }
 $this->visit($vertex);
 }
 $sortedList = $this->sortedNodeList;
 $this->nodeList = [];
 $this->sortedNodeList = [];
 return array_reverse($sortedList);
 }
 private function visit(Vertex $vertex) : void
 {
 $vertex->state = VertexState::IN_PROGRESS;
 foreach ($vertex->dependencyList as $edge) {
 $adjacentVertex = $this->nodeList[$edge->to];
 switch ($adjacentVertex->state) {
 case VertexState::VISITED:
 // Do nothing, since node was already visited
 break;
 case VertexState::IN_PROGRESS:
 if (isset($adjacentVertex->dependencyList[$vertex->hash]) && $adjacentVertex->dependencyList[$vertex->hash]->weight < $edge->weight) {
 // If we have some non-visited dependencies in the in-progress dependency, we
 // need to visit them before adding the node.
 foreach ($adjacentVertex->dependencyList as $adjacentEdge) {
 $adjacentEdgeVertex = $this->nodeList[$adjacentEdge->to];
 if ($adjacentEdgeVertex->state === VertexState::NOT_VISITED) {
 $this->visit($adjacentEdgeVertex);
 }
 }
 $adjacentVertex->state = VertexState::VISITED;
 $this->sortedNodeList[] = $adjacentVertex->value;
 }
 break;
 case VertexState::NOT_VISITED:
 $this->visit($adjacentVertex);
 }
 }
 if ($vertex->state !== VertexState::VISITED) {
 $vertex->state = VertexState::VISITED;
 $this->sortedNodeList[] = $vertex->value;
 }
 }
}
