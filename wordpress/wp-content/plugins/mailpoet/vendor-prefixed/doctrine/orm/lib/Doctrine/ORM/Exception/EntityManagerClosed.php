<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Exception;
if (!defined('ABSPATH')) exit;
final class EntityManagerClosed extends ORMException implements ManagerException
{
 public static function create() : self
 {
 return new self('The EntityManager is closed.');
 }
}
