<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema\Visitor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use MailPoetVendor\Doctrine\DBAL\Schema\Schema;
use MailPoetVendor\Doctrine\DBAL\Schema\Table;
use function current;
use function file_put_contents;
use function in_array;
use function strtolower;
class Graphviz extends AbstractVisitor
{
 private string $output = '';
 public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
 {
 $this->output .= $this->createNodeRelation($fkConstraint->getLocalTableName() . ':col' . current($fkConstraint->getLocalColumns()) . ':se', $fkConstraint->getForeignTableName() . ':col' . current($fkConstraint->getForeignColumns()) . ':se', ['dir' => 'back', 'arrowtail' => 'dot', 'arrowhead' => 'normal']);
 }
 public function acceptSchema(Schema $schema)
 {
 $this->output = 'digraph "' . $schema->getName() . '" {' . "\n";
 $this->output .= 'splines = true;' . "\n";
 $this->output .= 'overlap = false;' . "\n";
 $this->output .= 'outputorder=edgesfirst;' . "\n";
 $this->output .= 'mindist = 0.6;' . "\n";
 $this->output .= 'sep = .2;' . "\n";
 }
 public function acceptTable(Table $table)
 {
 $this->output .= $this->createNode($table->getName(), ['label' => $this->createTableLabel($table), 'shape' => 'plaintext']);
 }
 private function createTableLabel(Table $table) : string
 {
 // Start the table
 $label = '<<TABLE CELLSPACING="0" BORDER="1" ALIGN="LEFT">';
 // The title
 $label .= '<TR><TD BORDER="1" COLSPAN="3" ALIGN="CENTER" BGCOLOR="#fcaf3e">' . '<FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="12">' . $table->getName() . '</FONT></TD></TR>';
 // The attributes block
 foreach ($table->getColumns() as $column) {
 $columnLabel = $column->getName();
 $label .= '<TR>' . '<TD BORDER="0" ALIGN="LEFT" BGCOLOR="#eeeeec">' . '<FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="12">' . $columnLabel . '</FONT>' . '</TD>' . '<TD BORDER="0" ALIGN="LEFT" BGCOLOR="#eeeeec">' . '<FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10">' . strtolower($column->getType()->getName()) . '</FONT>' . '</TD>' . '<TD BORDER="0" ALIGN="RIGHT" BGCOLOR="#eeeeec" PORT="col' . $column->getName() . '">';
 $primaryKey = $table->getPrimaryKey();
 if ($primaryKey !== null && in_array($column->getName(), $primaryKey->getColumns(), \true)) {
 $label .= "✷";
 }
 $label .= '</TD></TR>';
 }
 // End the table
 $label .= '</TABLE>>';
 return $label;
 }
 private function createNode($name, $options) : string
 {
 $node = $name . ' [';
 foreach ($options as $key => $value) {
 $node .= $key . '=' . $value . ' ';
 }
 $node .= "]\n";
 return $node;
 }
 private function createNodeRelation($node1, $node2, $options) : string
 {
 $relation = $node1 . ' -> ' . $node2 . ' [';
 foreach ($options as $key => $value) {
 $relation .= $key . '=' . $value . ' ';
 }
 $relation .= "]\n";
 return $relation;
 }
 public function getOutput()
 {
 return $this->output . '}';
 }
 public function write($filename)
 {
 file_put_contents($filename, $this->getOutput());
 }
}
