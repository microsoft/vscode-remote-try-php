<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal\Hydration;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
use function implode;
use function sprintf;
class HydrationException extends ORMException
{
 public static function nonUniqueResult()
 {
 return new self('The result returned by the query was not unique.');
 }
 public static function parentObjectOfRelationNotFound($alias, $parentAlias)
 {
 return new self(sprintf("The parent object of entity result with alias '%s' was not found." . " The parent alias is '%s'.", $alias, $parentAlias));
 }
 public static function emptyDiscriminatorValue($dqlAlias)
 {
 return new self("The DQL alias '" . $dqlAlias . "' contains an entity " . 'of an inheritance hierarchy with an empty discriminator value. This means ' . 'that the database contains inconsistent data with an empty ' . 'discriminator value in a table row.');
 }
 public static function missingDiscriminatorColumn($entityName, $discrColumnName, $dqlAlias)
 {
 return new self(sprintf('The discriminator column "%s" is missing for "%s" using the DQL alias "%s".', $discrColumnName, $entityName, $dqlAlias));
 }
 public static function missingDiscriminatorMetaMappingColumn($entityName, $discrColumnName, $dqlAlias)
 {
 return new self(sprintf('The meta mapping for the discriminator column "%s" is missing for "%s" using the DQL alias "%s".', $discrColumnName, $entityName, $dqlAlias));
 }
 public static function invalidDiscriminatorValue($discrValue, $discrValues)
 {
 return new self(sprintf('The discriminator value "%s" is invalid. It must be one of "%s".', $discrValue, implode('", "', $discrValues)));
 }
}
