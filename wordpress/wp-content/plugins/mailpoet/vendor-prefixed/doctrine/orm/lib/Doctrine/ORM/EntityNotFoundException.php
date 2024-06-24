<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
use function implode;
use function sprintf;
class EntityNotFoundException extends ORMException
{
 public static function fromClassNameAndIdentifier($className, array $id)
 {
 $ids = [];
 foreach ($id as $key => $value) {
 $ids[] = $key . '(' . $value . ')';
 }
 return new self('Entity of type \'' . $className . '\'' . ($ids ? ' for IDs ' . implode(', ', $ids) : '') . ' was not found');
 }
 public static function noIdentifierFound(string $className) : self
 {
 return new self(sprintf('Unable to find "%s" entity identifier associated with the UnitOfWork', $className));
 }
}
