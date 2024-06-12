<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Configuration;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Query\Filter\SQLFilter;
use InvalidArgumentException;
use function assert;
use function ksort;
class FilterCollection
{
 public const FILTERS_STATE_CLEAN = 1;
 public const FILTERS_STATE_DIRTY = 2;
 private $config;
 private $em;
 private $enabledFilters = [];
 private $filterHash;
 private $filtersState = self::FILTERS_STATE_CLEAN;
 public function __construct(EntityManagerInterface $em)
 {
 $this->em = $em;
 $this->config = $em->getConfiguration();
 }
 public function getEnabledFilters()
 {
 return $this->enabledFilters;
 }
 public function enable($name)
 {
 if (!$this->has($name)) {
 throw new InvalidArgumentException("Filter '" . $name . "' does not exist.");
 }
 if (!$this->isEnabled($name)) {
 $filterClass = $this->config->getFilterClassName($name);
 assert($filterClass !== null);
 $this->enabledFilters[$name] = new $filterClass($this->em);
 // Keep the enabled filters sorted for the hash
 ksort($this->enabledFilters);
 $this->setFiltersStateDirty();
 }
 return $this->enabledFilters[$name];
 }
 public function disable($name)
 {
 // Get the filter to return it
 $filter = $this->getFilter($name);
 unset($this->enabledFilters[$name]);
 $this->setFiltersStateDirty();
 return $filter;
 }
 public function getFilter($name)
 {
 if (!$this->isEnabled($name)) {
 throw new InvalidArgumentException("Filter '" . $name . "' is not enabled.");
 }
 return $this->enabledFilters[$name];
 }
 public function has($name)
 {
 return $this->config->getFilterClassName($name) !== null;
 }
 public function isEnabled($name)
 {
 return isset($this->enabledFilters[$name]);
 }
 public function isClean()
 {
 return $this->filtersState === self::FILTERS_STATE_CLEAN;
 }
 public function getHash()
 {
 // If there are only clean filters, the previous hash can be returned
 if ($this->filtersState === self::FILTERS_STATE_CLEAN) {
 return $this->filterHash;
 }
 $filterHash = '';
 foreach ($this->enabledFilters as $name => $filter) {
 $filterHash .= $name . $filter;
 }
 $this->filterHash = $filterHash;
 $this->filtersState = self::FILTERS_STATE_CLEAN;
 return $filterHash;
 }
 public function setFiltersStateDirty()
 {
 $this->filtersState = self::FILTERS_STATE_DIRTY;
 }
}
