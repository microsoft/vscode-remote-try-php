<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
class ConnectionException extends Exception
{
 public static function commitFailedRollbackOnly()
 {
 return new self('Transaction commit failed because the transaction has been marked for rollback only.');
 }
 public static function noActiveTransaction()
 {
 return new self('There is no active transaction.');
 }
 public static function savepointsNotSupported()
 {
 return new self('Savepoints are not supported by this driver.');
 }
 public static function mayNotAlterNestedTransactionWithSavepointsInTransaction()
 {
 return new self('May not alter the nested transaction with savepoints behavior while a transaction is open.');
 }
}
