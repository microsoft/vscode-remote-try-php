<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema\Visitor;
if (!defined('ABSPATH')) exit;
interface NamespaceVisitor
{
 public function acceptNamespace($namespaceName);
}
