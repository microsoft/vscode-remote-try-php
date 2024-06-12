<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use PDO;
use Traversable;
interface ResultStatement extends Traversable
{
 public function closeCursor();
 public function columnCount();
 public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null);
 public function fetch($fetchMode = null, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0);
 public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null);
 public function fetchColumn($columnIndex = 0);
}
