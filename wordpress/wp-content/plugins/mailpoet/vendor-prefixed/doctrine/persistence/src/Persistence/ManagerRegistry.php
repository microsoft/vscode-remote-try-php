<?php
namespace MailPoetVendor\Doctrine\Persistence;
if (!defined('ABSPATH')) exit;
interface ManagerRegistry extends ConnectionRegistry
{
 public function getDefaultManagerName();
 public function getManager($name = null);
 public function getManagers();
 public function resetManager($name = null);
 public function getAliasNamespace($alias);
 public function getManagerNames();
 public function getRepository($persistentObject, $persistentManagerName = null);
 public function getManagerForClass($class);
}
