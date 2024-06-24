<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Id;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use Serializable;
use function serialize;
use function unserialize;
class SequenceGenerator extends AbstractIdGenerator implements Serializable
{
 private $_allocationSize;
 private $_sequenceName;
 private $_nextValue = 0;
 private $_maxValue = null;
 public function __construct($sequenceName, $allocationSize)
 {
 $this->_sequenceName = $sequenceName;
 $this->_allocationSize = $allocationSize;
 }
 public function generateId(EntityManagerInterface $em, $entity)
 {
 if ($this->_maxValue === null || $this->_nextValue === $this->_maxValue) {
 // Allocate new values
 $connection = $em->getConnection();
 $sql = $connection->getDatabasePlatform()->getSequenceNextValSQL($this->_sequenceName);
 if ($connection instanceof PrimaryReadReplicaConnection) {
 $connection->ensureConnectedToPrimary();
 }
 $this->_nextValue = (int) $connection->fetchOne($sql);
 $this->_maxValue = $this->_nextValue + $this->_allocationSize;
 }
 return $this->_nextValue++;
 }
 public function getCurrentMaxValue()
 {
 return $this->_maxValue;
 }
 public function getNextValue()
 {
 return $this->_nextValue;
 }
 public function serialize()
 {
 return serialize($this->__serialize());
 }
 public function __serialize() : array
 {
 return ['allocationSize' => $this->_allocationSize, 'sequenceName' => $this->_sequenceName];
 }
 public function unserialize($serialized)
 {
 $this->__unserialize(unserialize($serialized));
 }
 public function __unserialize(array $data) : void
 {
 $this->_sequenceName = $data['sequenceName'];
 $this->_allocationSize = $data['allocationSize'];
 }
}
