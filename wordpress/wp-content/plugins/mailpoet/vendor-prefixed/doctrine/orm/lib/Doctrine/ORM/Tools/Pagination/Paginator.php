<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Tools\Pagination;
if (!defined('ABSPATH')) exit;
use ArrayIterator;
use Countable;
use MailPoetVendor\Doctrine\Common\Collections\Collection;
use MailPoetVendor\Doctrine\ORM\Internal\SQLResultCasing;
use MailPoetVendor\Doctrine\ORM\NoResultException;
use MailPoetVendor\Doctrine\ORM\Query;
use MailPoetVendor\Doctrine\ORM\Query\Parameter;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;
use IteratorAggregate;
use ReturnTypeWillChange;
use function array_key_exists;
use function array_map;
use function array_sum;
use function count;
class Paginator implements Countable, IteratorAggregate
{
 use SQLResultCasing;
 private $query;
 private $fetchJoinCollection;
 private $useOutputWalkers;
 private $count;
 public function __construct($query, $fetchJoinCollection = \true)
 {
 if ($query instanceof QueryBuilder) {
 $query = $query->getQuery();
 }
 $this->query = $query;
 $this->fetchJoinCollection = (bool) $fetchJoinCollection;
 }
 public function getQuery()
 {
 return $this->query;
 }
 public function getFetchJoinCollection()
 {
 return $this->fetchJoinCollection;
 }
 public function getUseOutputWalkers()
 {
 return $this->useOutputWalkers;
 }
 public function setUseOutputWalkers($useOutputWalkers)
 {
 $this->useOutputWalkers = $useOutputWalkers;
 return $this;
 }
 #[\ReturnTypeWillChange]
 public function count()
 {
 if ($this->count === null) {
 try {
 $this->count = (int) array_sum(array_map('current', $this->getCountQuery()->getScalarResult()));
 } catch (NoResultException $e) {
 $this->count = 0;
 }
 }
 return $this->count;
 }
 #[\ReturnTypeWillChange]
 public function getIterator()
 {
 $offset = $this->query->getFirstResult();
 $length = $this->query->getMaxResults();
 if ($this->fetchJoinCollection && $length !== null) {
 $subQuery = $this->cloneQuery($this->query);
 if ($this->useOutputWalker($subQuery)) {
 $subQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, LimitSubqueryOutputWalker::class);
 } else {
 $this->appendTreeWalker($subQuery, LimitSubqueryWalker::class);
 $this->unbindUnusedQueryParams($subQuery);
 }
 $subQuery->setFirstResult($offset)->setMaxResults($length);
 $foundIdRows = $subQuery->getScalarResult();
 // don't do this for an empty id array
 if ($foundIdRows === []) {
 return new ArrayIterator([]);
 }
 $whereInQuery = $this->cloneQuery($this->query);
 $ids = array_map('current', $foundIdRows);
 $this->appendTreeWalker($whereInQuery, WhereInWalker::class);
 $whereInQuery->setHint(WhereInWalker::HINT_PAGINATOR_ID_COUNT, count($ids));
 $whereInQuery->setFirstResult(null)->setMaxResults(null);
 $whereInQuery->setParameter(WhereInWalker::PAGINATOR_ID_ALIAS, $ids);
 $whereInQuery->setCacheable($this->query->isCacheable());
 $whereInQuery->expireQueryCache();
 $result = $whereInQuery->getResult($this->query->getHydrationMode());
 } else {
 $result = $this->cloneQuery($this->query)->setMaxResults($length)->setFirstResult($offset)->setCacheable($this->query->isCacheable())->getResult($this->query->getHydrationMode());
 }
 return new ArrayIterator($result);
 }
 private function cloneQuery(Query $query) : Query
 {
 $cloneQuery = clone $query;
 $cloneQuery->setParameters(clone $query->getParameters());
 $cloneQuery->setCacheable(\false);
 foreach ($query->getHints() as $name => $value) {
 $cloneQuery->setHint($name, $value);
 }
 return $cloneQuery;
 }
 private function useOutputWalker(Query $query) : bool
 {
 if ($this->useOutputWalkers === null) {
 return (bool) $query->getHint(Query::HINT_CUSTOM_OUTPUT_WALKER) === \false;
 }
 return $this->useOutputWalkers;
 }
 private function appendTreeWalker(Query $query, string $walkerClass) : void
 {
 $hints = $query->getHint(Query::HINT_CUSTOM_TREE_WALKERS);
 if ($hints === \false) {
 $hints = [];
 }
 $hints[] = $walkerClass;
 $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, $hints);
 }
 private function getCountQuery() : Query
 {
 $countQuery = $this->cloneQuery($this->query);
 if (!$countQuery->hasHint(CountWalker::HINT_DISTINCT)) {
 $countQuery->setHint(CountWalker::HINT_DISTINCT, \true);
 }
 if ($this->useOutputWalker($countQuery)) {
 $platform = $countQuery->getEntityManager()->getConnection()->getDatabasePlatform();
 // law of demeter win
 $rsm = new ResultSetMapping();
 $rsm->addScalarResult($this->getSQLResultCasing($platform, 'dctrn_count'), 'count');
 $countQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, CountOutputWalker::class);
 $countQuery->setResultSetMapping($rsm);
 } else {
 $this->appendTreeWalker($countQuery, CountWalker::class);
 $this->unbindUnusedQueryParams($countQuery);
 }
 $countQuery->setFirstResult(null)->setMaxResults(null);
 return $countQuery;
 }
 private function unbindUnusedQueryParams(Query $query) : void
 {
 $parser = new Parser($query);
 $parameterMappings = $parser->parse()->getParameterMappings();
 $parameters = $query->getParameters();
 foreach ($parameters as $key => $parameter) {
 $parameterName = $parameter->getName();
 if (!(isset($parameterMappings[$parameterName]) || array_key_exists($parameterName, $parameterMappings))) {
 unset($parameters[$key]);
 }
 }
 $query->setParameters($parameters);
 }
}
