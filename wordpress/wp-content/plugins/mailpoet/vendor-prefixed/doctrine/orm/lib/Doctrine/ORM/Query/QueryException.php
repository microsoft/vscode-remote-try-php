<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
use MailPoetVendor\Doctrine\ORM\Query\AST\PathExpression;
use Exception;
class QueryException extends ORMException
{
 public static function dqlError($dql)
 {
 return new self($dql);
 }
 public static function syntaxError($message, $previous = null)
 {
 return new self('[Syntax Error] ' . $message, 0, $previous);
 }
 public static function semanticalError($message, $previous = null)
 {
 return new self('[Semantical Error] ' . $message, 0, $previous);
 }
 public static function invalidLockMode()
 {
 return new self('Invalid lock mode hint provided.');
 }
 public static function invalidParameterType($expected, $received)
 {
 return new self('Invalid parameter type, ' . $received . ' given, but ' . $expected . ' expected.');
 }
 public static function invalidParameterPosition($pos)
 {
 return new self('Invalid parameter position: ' . $pos);
 }
 public static function tooManyParameters($expected, $received)
 {
 return new self('Too many parameters: the query defines ' . $expected . ' parameters and you bound ' . $received);
 }
 public static function tooFewParameters($expected, $received)
 {
 return new self('Too few parameters: the query defines ' . $expected . ' parameters but you only bound ' . $received);
 }
 public static function invalidParameterFormat($value)
 {
 return new self('Invalid parameter format, ' . $value . ' given, but :<name> or ?<num> expected.');
 }
 public static function unknownParameter($key)
 {
 return new self('Invalid parameter: token ' . $key . ' is not defined in the query.');
 }
 public static function parameterTypeMismatch()
 {
 return new self('DQL Query parameter and type numbers mismatch, but have to be exactly equal.');
 }
 public static function invalidPathExpression($pathExpr)
 {
 return new self("Invalid PathExpression '" . $pathExpr->identificationVariable . '.' . $pathExpr->field . "'.");
 }
 public static function invalidLiteral($literal)
 {
 return new self("Invalid literal '" . $literal . "'");
 }
 public static function iterateWithFetchJoinCollectionNotAllowed($assoc)
 {
 return new self('Invalid query operation: Not allowed to iterate over fetch join collections ' . 'in class ' . $assoc['sourceEntity'] . ' association ' . $assoc['fieldName']);
 }
 public static function partialObjectsAreDangerous()
 {
 return new self('Loading partial objects is dangerous. Fetch full objects or consider ' . 'using a different fetch mode. If you really want partial objects, ' . 'set the doctrine.forcePartialLoad query hint to TRUE.');
 }
 public static function overwritingJoinConditionsNotYetSupported($assoc)
 {
 return new self('Unsupported query operation: It is not yet possible to overwrite the join ' . 'conditions in class ' . $assoc['sourceEntityName'] . ' association ' . $assoc['fieldName'] . '. ' . 'Use WITH to append additional join conditions to the association.');
 }
 public static function associationPathInverseSideNotSupported(PathExpression $pathExpr)
 {
 return new self('A single-valued association path expression to an inverse side is not supported in DQL queries. ' . 'Instead of "' . $pathExpr->identificationVariable . '.' . $pathExpr->field . '" use an explicit join.');
 }
 public static function iterateWithFetchJoinNotAllowed($assoc)
 {
 return new self('Iterate with fetch join in class ' . $assoc['sourceEntity'] . ' using association ' . $assoc['fieldName'] . ' not allowed.');
 }
 public static function iterateWithMixedResultNotAllowed() : QueryException
 {
 return new self('Iterating a query with mixed results (using scalars) is not supported.');
 }
 public static function associationPathCompositeKeyNotSupported()
 {
 return new self('A single-valued association path expression to an entity with a composite primary ' . 'key is not supported. Explicitly name the components of the composite primary key ' . 'in the query.');
 }
 public static function instanceOfUnrelatedClass($className, $rootClass)
 {
 return new self("Cannot check if a child of '" . $rootClass . "' is instanceof '" . $className . "', " . 'inheritance hierarchy does not exists between these two classes.');
 }
 public static function invalidQueryComponent($dqlAlias)
 {
 return new self("Invalid query component given for DQL alias '" . $dqlAlias . "', " . "requires 'metadata', 'parent', 'relation', 'map', 'nestingLevel' and 'token' keys.");
 }
}
