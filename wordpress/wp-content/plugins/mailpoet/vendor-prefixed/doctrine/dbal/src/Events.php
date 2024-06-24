<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
final class Events
{
 private function __construct()
 {
 }
 public const postConnect = 'postConnect';
 public const onSchemaCreateTable = 'onSchemaCreateTable';
 public const onSchemaCreateTableColumn = 'onSchemaCreateTableColumn';
 public const onSchemaDropTable = 'onSchemaDropTable';
 public const onSchemaAlterTable = 'onSchemaAlterTable';
 public const onSchemaAlterTableAddColumn = 'onSchemaAlterTableAddColumn';
 public const onSchemaAlterTableRemoveColumn = 'onSchemaAlterTableRemoveColumn';
 public const onSchemaAlterTableChangeColumn = 'onSchemaAlterTableChangeColumn';
 public const onSchemaAlterTableRenameColumn = 'onSchemaAlterTableRenameColumn';
 public const onSchemaColumnDefinition = 'onSchemaColumnDefinition';
 public const onSchemaIndexDefinition = 'onSchemaIndexDefinition';
 public const onTransactionBegin = 'onTransactionBegin';
 public const onTransactionCommit = 'onTransactionCommit';
 public const onTransactionRollBack = 'onTransactionRollBack';
}
