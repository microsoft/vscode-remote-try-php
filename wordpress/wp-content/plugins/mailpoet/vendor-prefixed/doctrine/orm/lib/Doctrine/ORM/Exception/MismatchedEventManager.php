<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Exception;
if (!defined('ABSPATH')) exit;
final class MismatchedEventManager extends ORMException implements ManagerException
{
 public static function create() : self
 {
 return new self('Cannot use different EventManager instances for EntityManager and Connection.');
 }
}
