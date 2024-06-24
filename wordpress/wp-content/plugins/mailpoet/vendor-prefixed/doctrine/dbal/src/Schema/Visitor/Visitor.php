<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema\Visitor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\Column;
use MailPoetVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use MailPoetVendor\Doctrine\DBAL\Schema\Index;
use MailPoetVendor\Doctrine\DBAL\Schema\Schema;
use MailPoetVendor\Doctrine\DBAL\Schema\SchemaException;
use MailPoetVendor\Doctrine\DBAL\Schema\Sequence;
use MailPoetVendor\Doctrine\DBAL\Schema\Table;
interface Visitor
{
 public function acceptSchema(Schema $schema);
 public function acceptTable(Table $table);
 public function acceptColumn(Table $table, Column $column);
 public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint);
 public function acceptIndex(Table $table, Index $index);
 public function acceptSequence(Sequence $sequence);
}
