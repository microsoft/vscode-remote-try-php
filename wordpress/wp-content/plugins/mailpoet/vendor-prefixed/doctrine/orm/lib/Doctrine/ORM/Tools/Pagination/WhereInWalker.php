<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Tools\Pagination;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Query\AST\ArithmeticExpression;
use MailPoetVendor\Doctrine\ORM\Query\AST\ConditionalExpression;
use MailPoetVendor\Doctrine\ORM\Query\AST\ConditionalFactor;
use MailPoetVendor\Doctrine\ORM\Query\AST\ConditionalPrimary;
use MailPoetVendor\Doctrine\ORM\Query\AST\ConditionalTerm;
use MailPoetVendor\Doctrine\ORM\Query\AST\InExpression;
use MailPoetVendor\Doctrine\ORM\Query\AST\InputParameter;
use MailPoetVendor\Doctrine\ORM\Query\AST\NullComparisonExpression;
use MailPoetVendor\Doctrine\ORM\Query\AST\PathExpression;
use MailPoetVendor\Doctrine\ORM\Query\AST\SelectStatement;
use MailPoetVendor\Doctrine\ORM\Query\AST\SimpleArithmeticExpression;
use MailPoetVendor\Doctrine\ORM\Query\AST\WhereClause;
use MailPoetVendor\Doctrine\ORM\Query\TreeWalkerAdapter;
use MailPoetVendor\Doctrine\ORM\Utility\PersisterHelper;
use RuntimeException;
use function array_map;
use function assert;
use function count;
use function is_array;
use function reset;
class WhereInWalker extends TreeWalkerAdapter
{
 public const HINT_PAGINATOR_ID_COUNT = 'doctrine.id.count';
 public const PAGINATOR_ID_ALIAS = 'dpid';
 public function walkSelectStatement(SelectStatement $AST)
 {
 $queryComponents = $this->_getQueryComponents();
 // Get the root entity and alias from the AST fromClause
 $from = $AST->fromClause->identificationVariableDeclarations;
 if (count($from) > 1) {
 throw new RuntimeException('Cannot count query which selects two FROM components, cannot make distinction');
 }
 $fromRoot = reset($from);
 $rootAlias = $fromRoot->rangeVariableDeclaration->aliasIdentificationVariable;
 $rootClass = $queryComponents[$rootAlias]['metadata'];
 assert($rootClass instanceof ClassMetadata);
 $identifierFieldName = $rootClass->getSingleIdentifierFieldName();
 $pathType = PathExpression::TYPE_STATE_FIELD;
 if (isset($rootClass->associationMappings[$identifierFieldName])) {
 $pathType = PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION;
 }
 $pathExpression = new PathExpression(PathExpression::TYPE_STATE_FIELD | PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION, $rootAlias, $identifierFieldName);
 $pathExpression->type = $pathType;
 $count = $this->_getQuery()->getHint(self::HINT_PAGINATOR_ID_COUNT);
 if ($count > 0) {
 $arithmeticExpression = new ArithmeticExpression();
 $arithmeticExpression->simpleArithmeticExpression = new SimpleArithmeticExpression([$pathExpression]);
 $expression = new InExpression($arithmeticExpression);
 $expression->literals[] = new InputParameter(':' . self::PAGINATOR_ID_ALIAS);
 $this->convertWhereInIdentifiersToDatabaseValue(PersisterHelper::getTypeOfField($identifierFieldName, $rootClass, $this->_getQuery()->getEntityManager())[0]);
 } else {
 $expression = new NullComparisonExpression($pathExpression);
 $expression->not = \false;
 }
 $conditionalPrimary = new ConditionalPrimary();
 $conditionalPrimary->simpleConditionalExpression = $expression;
 if ($AST->whereClause) {
 if ($AST->whereClause->conditionalExpression instanceof ConditionalTerm) {
 $AST->whereClause->conditionalExpression->conditionalFactors[] = $conditionalPrimary;
 } elseif ($AST->whereClause->conditionalExpression instanceof ConditionalPrimary) {
 $AST->whereClause->conditionalExpression = new ConditionalExpression([new ConditionalTerm([$AST->whereClause->conditionalExpression, $conditionalPrimary])]);
 } elseif ($AST->whereClause->conditionalExpression instanceof ConditionalExpression || $AST->whereClause->conditionalExpression instanceof ConditionalFactor) {
 $tmpPrimary = new ConditionalPrimary();
 $tmpPrimary->conditionalExpression = $AST->whereClause->conditionalExpression;
 $AST->whereClause->conditionalExpression = new ConditionalTerm([$tmpPrimary, $conditionalPrimary]);
 }
 } else {
 $AST->whereClause = new WhereClause(new ConditionalExpression([new ConditionalTerm([$conditionalPrimary])]));
 }
 }
 private function convertWhereInIdentifiersToDatabaseValue(string $type) : void
 {
 $query = $this->_getQuery();
 $identifiersParameter = $query->getParameter(self::PAGINATOR_ID_ALIAS);
 assert($identifiersParameter !== null);
 $identifiers = $identifiersParameter->getValue();
 assert(is_array($identifiers));
 $connection = $this->_getQuery()->getEntityManager()->getConnection();
 $query->setParameter(self::PAGINATOR_ID_ALIAS, array_map(static function ($id) use($connection, $type) {
 return $connection->convertToDatabaseValue($id, $type);
 }, $identifiers));
 }
}
