<?php
namespace MailPoetVendor\Doctrine\DBAL\Event\Listeners;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventSubscriber;
use MailPoetVendor\Doctrine\DBAL\Event\ConnectionEventArgs;
use MailPoetVendor\Doctrine\DBAL\Events;
class SQLSessionInit implements EventSubscriber
{
 protected $sql;
 public function __construct($sql)
 {
 $this->sql = $sql;
 }
 public function postConnect(ConnectionEventArgs $args)
 {
 $conn = $args->getConnection();
 $conn->exec($this->sql);
 }
 public function getSubscribedEvents()
 {
 return [Events::postConnect];
 }
}
