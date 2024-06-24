<?php
namespace MailPoetVendor\Doctrine\DBAL\Event\Listeners;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventSubscriber;
use MailPoetVendor\Doctrine\DBAL\Event\ConnectionEventArgs;
use MailPoetVendor\Doctrine\DBAL\Events;
use MailPoetVendor\Doctrine\DBAL\Exception;
class SQLiteSessionInit implements EventSubscriber
{
 public function postConnect(ConnectionEventArgs $args)
 {
 $args->getConnection()->executeStatement('PRAGMA foreign_keys=ON');
 }
 public function getSubscribedEvents()
 {
 return [Events::postConnect];
 }
}
