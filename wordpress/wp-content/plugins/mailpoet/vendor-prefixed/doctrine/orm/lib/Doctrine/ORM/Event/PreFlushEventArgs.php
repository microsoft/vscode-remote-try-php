<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventArgs;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
class PreFlushEventArgs extends EventArgs
{
 private $em;
 public function __construct(EntityManagerInterface $em)
 {
 $this->em = $em;
 }
 public function getEntityManager()
 {
 return $this->em;
 }
}
