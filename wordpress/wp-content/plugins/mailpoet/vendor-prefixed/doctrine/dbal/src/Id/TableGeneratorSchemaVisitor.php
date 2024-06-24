<?php
namespace MailPoetVendor\Doctrine\DBAL\Id;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\Column;
use MailPoetVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use MailPoetVendor\Doctrine\DBAL\Schema\Index;
use MailPoetVendor\Doctrine\DBAL\Schema\Schema;
use MailPoetVendor\Doctrine\DBAL\Schema\Sequence;
use MailPoetVendor\Doctrine\DBAL\Schema\Table;
use MailPoetVendor\Doctrine\DBAL\Schema\Visitor\Visitor;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
class TableGeneratorSchemaVisitor implements Visitor
{
 private $generatorTableName;
 public function __construct($generatorTableName = 'sequences')
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4681', 'The TableGeneratorSchemaVisitor class is is deprecated.');
 $this->generatorTableName = $generatorTableName;
 }
 public function acceptSchema(Schema $schema)
 {
 $table = $schema->createTable($this->generatorTableName);
 $table->addColumn('sequence_name', 'string');
 $table->addColumn('sequence_value', 'integer', ['default' => 1]);
 $table->addColumn('sequence_increment_by', 'integer', ['default' => 1]);
 }
 public function acceptTable(Table $table)
 {
 }
 public function acceptColumn(Table $table, Column $column)
 {
 }
 public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
 {
 }
 public function acceptIndex(Table $table, Index $index)
 {
 }
 public function acceptSequence(Sequence $sequence)
 {
 }
}
