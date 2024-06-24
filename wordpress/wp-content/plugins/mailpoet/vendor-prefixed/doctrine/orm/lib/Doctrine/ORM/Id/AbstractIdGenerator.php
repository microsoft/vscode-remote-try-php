<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Id;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use LogicException;
use function get_debug_type;
use function sprintf;
abstract class AbstractIdGenerator
{
 private $alreadyDelegatedToGenerateId = \false;
 public function generate(EntityManager $em, $entity)
 {
 if ($this->alreadyDelegatedToGenerateId) {
 throw new LogicException(sprintf('Endless recursion detected in %s. Please implement generateId() without calling the parent implementation.', get_debug_type($this)));
 }
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9325', '%s::generate() is deprecated, call generateId() instead.', get_debug_type($this));
 $this->alreadyDelegatedToGenerateId = \true;
 try {
 return $this->generateId($em, $entity);
 } finally {
 $this->alreadyDelegatedToGenerateId = \false;
 }
 }
 public function generateId(EntityManagerInterface $em, $entity)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9325', 'Not implementing %s in %s is deprecated.', __FUNCTION__, get_debug_type($this));
 if (!$em instanceof EntityManager) {
 throw new InvalidArgumentException('Unsupported entity manager implementation.');
 }
 return $this->generate($em, $entity);
 }
 public function isPostInsertGenerator()
 {
 return \false;
 }
}
