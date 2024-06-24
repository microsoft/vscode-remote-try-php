<?php
namespace MailPoetVendor\Doctrine\DBAL\SQL\Builder;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Schema\Schema;
use MailPoetVendor\Doctrine\DBAL\Schema\Sequence;
use MailPoetVendor\Doctrine\DBAL\Schema\Table;
use function array_merge;
final class CreateSchemaObjectsSQLBuilder
{
 private AbstractPlatform $platform;
 public function __construct(AbstractPlatform $platform)
 {
 $this->platform = $platform;
 }
 public function buildSQL(Schema $schema) : array
 {
 return array_merge($this->buildNamespaceStatements($schema->getNamespaces()), $this->buildSequenceStatements($schema->getSequences()), $this->buildTableStatements($schema->getTables()));
 }
 private function buildNamespaceStatements(array $namespaces) : array
 {
 $statements = [];
 if ($this->platform->supportsSchemas()) {
 foreach ($namespaces as $namespace) {
 $statements[] = $this->platform->getCreateSchemaSQL($namespace);
 }
 }
 return $statements;
 }
 private function buildTableStatements(array $tables) : array
 {
 return $this->platform->getCreateTablesSQL($tables);
 }
 private function buildSequenceStatements(array $sequences) : array
 {
 $statements = [];
 foreach ($sequences as $sequence) {
 $statements[] = $this->platform->getCreateSequenceSQL($sequence);
 }
 return $statements;
 }
}
