<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms\Keywords;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\Column;
use MailPoetVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use MailPoetVendor\Doctrine\DBAL\Schema\Index;
use MailPoetVendor\Doctrine\DBAL\Schema\Schema;
use MailPoetVendor\Doctrine\DBAL\Schema\Sequence;
use MailPoetVendor\Doctrine\DBAL\Schema\Table;
use MailPoetVendor\Doctrine\DBAL\Schema\Visitor\Visitor;
use function implode;
use function str_replace;
class ReservedKeywordsValidator implements Visitor
{
 private $keywordLists = [];
 private $violations = [];
 public function __construct(array $keywordLists)
 {
 $this->keywordLists = $keywordLists;
 }
 public function getViolations()
 {
 return $this->violations;
 }
 private function isReservedWord($word)
 {
 if ($word[0] === '`') {
 $word = str_replace('`', '', $word);
 }
 $keywordLists = [];
 foreach ($this->keywordLists as $keywordList) {
 if (!$keywordList->isKeyword($word)) {
 continue;
 }
 $keywordLists[] = $keywordList->getName();
 }
 return $keywordLists;
 }
 private function addViolation($asset, $violatedPlatforms)
 {
 if (!$violatedPlatforms) {
 return;
 }
 $this->violations[] = $asset . ' keyword violations: ' . implode(', ', $violatedPlatforms);
 }
 public function acceptColumn(Table $table, Column $column)
 {
 $this->addViolation('Table ' . $table->getName() . ' column ' . $column->getName(), $this->isReservedWord($column->getName()));
 }
 public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
 {
 }
 public function acceptIndex(Table $table, Index $index)
 {
 }
 public function acceptSchema(Schema $schema)
 {
 }
 public function acceptSequence(Sequence $sequence)
 {
 }
 public function acceptTable(Table $table)
 {
 $this->addViolation('Table ' . $table->getName(), $this->isReservedWord($table->getName()));
 }
}
