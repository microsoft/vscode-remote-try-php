<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\AST\Node;
use MailPoetVendor\Doctrine\ORM\Query\Lexer;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\QueryException;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
use function strtolower;
class DateAddFunction extends FunctionNode
{
 public $firstDateExpression = null;
 public $intervalExpression = null;
 public $unit = null;
 public function getSql(SqlWalker $sqlWalker)
 {
 switch (strtolower($this->unit->value)) {
 case 'second':
 return $sqlWalker->getConnection()->getDatabasePlatform()->getDateAddSecondsExpression($this->firstDateExpression->dispatch($sqlWalker), $this->intervalExpression->dispatch($sqlWalker));
 case 'minute':
 return $sqlWalker->getConnection()->getDatabasePlatform()->getDateAddMinutesExpression($this->firstDateExpression->dispatch($sqlWalker), $this->intervalExpression->dispatch($sqlWalker));
 case 'hour':
 return $sqlWalker->getConnection()->getDatabasePlatform()->getDateAddHourExpression($this->firstDateExpression->dispatch($sqlWalker), $this->intervalExpression->dispatch($sqlWalker));
 case 'day':
 return $sqlWalker->getConnection()->getDatabasePlatform()->getDateAddDaysExpression($this->firstDateExpression->dispatch($sqlWalker), $this->intervalExpression->dispatch($sqlWalker));
 case 'week':
 return $sqlWalker->getConnection()->getDatabasePlatform()->getDateAddWeeksExpression($this->firstDateExpression->dispatch($sqlWalker), $this->intervalExpression->dispatch($sqlWalker));
 case 'month':
 return $sqlWalker->getConnection()->getDatabasePlatform()->getDateAddMonthExpression($this->firstDateExpression->dispatch($sqlWalker), $this->intervalExpression->dispatch($sqlWalker));
 case 'year':
 return $sqlWalker->getConnection()->getDatabasePlatform()->getDateAddYearsExpression($this->firstDateExpression->dispatch($sqlWalker), $this->intervalExpression->dispatch($sqlWalker));
 default:
 throw QueryException::semanticalError('DATE_ADD() only supports units of type second, minute, hour, day, week, month and year.');
 }
 }
 public function parse(Parser $parser)
 {
 $parser->match(Lexer::T_IDENTIFIER);
 $parser->match(Lexer::T_OPEN_PARENTHESIS);
 $this->firstDateExpression = $parser->ArithmeticPrimary();
 $parser->match(Lexer::T_COMMA);
 $this->intervalExpression = $parser->ArithmeticPrimary();
 $parser->match(Lexer::T_COMMA);
 $this->unit = $parser->StringPrimary();
 $parser->match(Lexer::T_CLOSE_PARENTHESIS);
 }
}
