<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\QueryException;
use function get_debug_type;
class ASTException extends QueryException
{
 public static function noDispatchForNode($node)
 {
 return new self('Double-dispatch for node ' . get_debug_type($node) . ' is not supported.');
 }
}
