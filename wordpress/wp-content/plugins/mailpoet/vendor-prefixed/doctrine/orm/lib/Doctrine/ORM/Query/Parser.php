<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Lexer\Token;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Query;
use MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
use LogicException;
use ReflectionClass;
use function array_intersect;
use function array_search;
use function assert;
use function class_exists;
use function count;
use function explode;
use function implode;
use function in_array;
use function interface_exists;
use function is_string;
use function sprintf;
use function str_contains;
use function strlen;
use function strpos;
use function strrpos;
use function strtolower;
use function substr;
class Parser
{
 private static $stringFunctions = ['concat' => Functions\ConcatFunction::class, 'substring' => Functions\SubstringFunction::class, 'trim' => Functions\TrimFunction::class, 'lower' => Functions\LowerFunction::class, 'upper' => Functions\UpperFunction::class, 'identity' => Functions\IdentityFunction::class];
 private static $numericFunctions = [
 'length' => Functions\LengthFunction::class,
 'locate' => Functions\LocateFunction::class,
 'abs' => Functions\AbsFunction::class,
 'sqrt' => Functions\SqrtFunction::class,
 'mod' => Functions\ModFunction::class,
 'size' => Functions\SizeFunction::class,
 'date_diff' => Functions\DateDiffFunction::class,
 'bit_and' => Functions\BitAndFunction::class,
 'bit_or' => Functions\BitOrFunction::class,
 // Aggregate functions
 'min' => Functions\MinFunction::class,
 'max' => Functions\MaxFunction::class,
 'avg' => Functions\AvgFunction::class,
 'sum' => Functions\SumFunction::class,
 'count' => Functions\CountFunction::class,
 ];
 private static $datetimeFunctions = ['current_date' => Functions\CurrentDateFunction::class, 'current_time' => Functions\CurrentTimeFunction::class, 'current_timestamp' => Functions\CurrentTimestampFunction::class, 'date_add' => Functions\DateAddFunction::class, 'date_sub' => Functions\DateSubFunction::class];
 private $deferredIdentificationVariables = [];
 private $deferredPartialObjectExpressions = [];
 private $deferredPathExpressions = [];
 private $deferredResultVariables = [];
 private $deferredNewObjectExpressions = [];
 private $lexer;
 private $parserResult;
 private $em;
 private $query;
 private $queryComponents = [];
 private $nestingLevel = 0;
 private $customTreeWalkers = [];
 private $customOutputWalker;
 private $identVariableExpressions = [];
 public function __construct(Query $query)
 {
 $this->query = $query;
 $this->em = $query->getEntityManager();
 $this->lexer = new Lexer((string) $query->getDQL());
 $this->parserResult = new ParserResult();
 }
 public function setCustomOutputTreeWalker($className)
 {
 $this->customOutputWalker = $className;
 }
 public function addCustomTreeWalker($className)
 {
 $this->customTreeWalkers[] = $className;
 }
 public function getLexer()
 {
 return $this->lexer;
 }
 public function getParserResult()
 {
 return $this->parserResult;
 }
 public function getEntityManager()
 {
 return $this->em;
 }
 public function getAST()
 {
 // Parse & build AST
 $AST = $this->QueryLanguage();
 // Process any deferred validations of some nodes in the AST.
 // This also allows post-processing of the AST for modification purposes.
 $this->processDeferredIdentificationVariables();
 if ($this->deferredPartialObjectExpressions) {
 $this->processDeferredPartialObjectExpressions();
 }
 if ($this->deferredPathExpressions) {
 $this->processDeferredPathExpressions();
 }
 if ($this->deferredResultVariables) {
 $this->processDeferredResultVariables();
 }
 if ($this->deferredNewObjectExpressions) {
 $this->processDeferredNewObjectExpressions($AST);
 }
 $this->processRootEntityAliasSelected();
 // TODO: Is there a way to remove this? It may impact the mixed hydration resultset a lot!
 $this->fixIdentificationVariableOrder($AST);
 return $AST;
 }
 public function match($token)
 {
 $lookaheadType = $this->lexer->lookahead['type'] ?? null;
 // Short-circuit on first condition, usually types match
 if ($lookaheadType === $token) {
 $this->lexer->moveNext();
 return;
 }
 // If parameter is not identifier (1-99) must be exact match
 if ($token < Lexer::T_IDENTIFIER) {
 $this->syntaxError($this->lexer->getLiteral($token));
 }
 // If parameter is keyword (200+) must be exact match
 if ($token > Lexer::T_IDENTIFIER) {
 $this->syntaxError($this->lexer->getLiteral($token));
 }
 // If parameter is T_IDENTIFIER, then matches T_IDENTIFIER (100) and keywords (200+)
 if ($token === Lexer::T_IDENTIFIER && $lookaheadType < Lexer::T_IDENTIFIER) {
 $this->syntaxError($this->lexer->getLiteral($token));
 }
 $this->lexer->moveNext();
 }
 public function free($deep = \false, $position = 0)
 {
 // WARNING! Use this method with care. It resets the scanner!
 $this->lexer->resetPosition($position);
 // Deep = true cleans peek and also any previously defined errors
 if ($deep) {
 $this->lexer->resetPeek();
 }
 $this->lexer->token = null;
 $this->lexer->lookahead = null;
 }
 public function parse()
 {
 $AST = $this->getAST();
 $customWalkers = $this->query->getHint(Query::HINT_CUSTOM_TREE_WALKERS);
 if ($customWalkers !== \false) {
 $this->customTreeWalkers = $customWalkers;
 }
 $customOutputWalker = $this->query->getHint(Query::HINT_CUSTOM_OUTPUT_WALKER);
 if ($customOutputWalker !== \false) {
 $this->customOutputWalker = $customOutputWalker;
 }
 // Run any custom tree walkers over the AST
 if ($this->customTreeWalkers) {
 $treeWalkerChain = new TreeWalkerChain($this->query, $this->parserResult, $this->queryComponents);
 foreach ($this->customTreeWalkers as $walker) {
 $treeWalkerChain->addTreeWalker($walker);
 }
 switch (\true) {
 case $AST instanceof AST\UpdateStatement:
 $treeWalkerChain->walkUpdateStatement($AST);
 break;
 case $AST instanceof AST\DeleteStatement:
 $treeWalkerChain->walkDeleteStatement($AST);
 break;
 case $AST instanceof AST\SelectStatement:
 default:
 $treeWalkerChain->walkSelectStatement($AST);
 }
 $this->queryComponents = $treeWalkerChain->getQueryComponents();
 }
 $outputWalkerClass = $this->customOutputWalker ?: SqlWalker::class;
 $outputWalker = new $outputWalkerClass($this->query, $this->parserResult, $this->queryComponents);
 // Assign an SQL executor to the parser result
 $this->parserResult->setSqlExecutor($outputWalker->getExecutor($AST));
 return $this->parserResult;
 }
 private function fixIdentificationVariableOrder(AST\Node $AST) : void
 {
 if (count($this->identVariableExpressions) <= 1) {
 return;
 }
 assert($AST instanceof AST\SelectStatement);
 foreach ($this->queryComponents as $dqlAlias => $qComp) {
 if (!isset($this->identVariableExpressions[$dqlAlias])) {
 continue;
 }
 $expr = $this->identVariableExpressions[$dqlAlias];
 $key = array_search($expr, $AST->selectClause->selectExpressions, \true);
 unset($AST->selectClause->selectExpressions[$key]);
 $AST->selectClause->selectExpressions[] = $expr;
 }
 }
 public function syntaxError($expected = '', $token = null)
 {
 if ($token === null) {
 $token = $this->lexer->lookahead;
 }
 $tokenPos = $token['position'] ?? '-1';
 $message = sprintf('line 0, col %d: Error: ', $tokenPos);
 $message .= $expected !== '' ? sprintf('Expected %s, got ', $expected) : 'Unexpected ';
 $message .= $this->lexer->lookahead === null ? 'end of string.' : sprintf("'%s'", $token['value']);
 throw QueryException::syntaxError($message, QueryException::dqlError($this->query->getDQL() ?? ''));
 }
 public function semanticalError($message = '', $token = null)
 {
 if ($token === null) {
 $token = $this->lexer->lookahead ?? ['position' => 0];
 }
 // Minimum exposed chars ahead of token
 $distance = 12;
 // Find a position of a final word to display in error string
 $dql = $this->query->getDQL();
 $length = strlen($dql);
 $pos = $token['position'] + $distance;
 $pos = strpos($dql, ' ', $length > $pos ? $pos : $length);
 $length = $pos !== \false ? $pos - $token['position'] : $distance;
 $tokenPos = $token['position'] > 0 ? $token['position'] : '-1';
 $tokenStr = substr($dql, $token['position'], $length);
 // Building informative message
 $message = 'line 0, col ' . $tokenPos . " near '" . $tokenStr . "': Error: " . $message;
 throw QueryException::semanticalError($message, QueryException::dqlError($this->query->getDQL()));
 }
 private function peekBeyondClosingParenthesis(bool $resetPeek = \true)
 {
 $token = $this->lexer->peek();
 $numUnmatched = 1;
 while ($numUnmatched > 0 && $token !== null) {
 switch ($token['type']) {
 case Lexer::T_OPEN_PARENTHESIS:
 ++$numUnmatched;
 break;
 case Lexer::T_CLOSE_PARENTHESIS:
 --$numUnmatched;
 break;
 default:
 }
 $token = $this->lexer->peek();
 }
 if ($resetPeek) {
 $this->lexer->resetPeek();
 }
 return $token;
 }
 private function isMathOperator($token) : bool
 {
 return $token !== null && in_array($token['type'], [Lexer::T_PLUS, Lexer::T_MINUS, Lexer::T_DIVIDE, Lexer::T_MULTIPLY], \true);
 }
 private function isFunction() : bool
 {
 assert($this->lexer->lookahead !== null);
 $lookaheadType = $this->lexer->lookahead['type'];
 $peek = $this->lexer->peek();
 $this->lexer->resetPeek();
 return $lookaheadType >= Lexer::T_IDENTIFIER && $peek !== null && $peek['type'] === Lexer::T_OPEN_PARENTHESIS;
 }
 private function isAggregateFunction(int $tokenType) : bool
 {
 return in_array($tokenType, [Lexer::T_AVG, Lexer::T_MIN, Lexer::T_MAX, Lexer::T_SUM, Lexer::T_COUNT], \true);
 }
 private function isNextAllAnySome() : bool
 {
 assert($this->lexer->lookahead !== null);
 return in_array($this->lexer->lookahead['type'], [Lexer::T_ALL, Lexer::T_ANY, Lexer::T_SOME], \true);
 }
 private function processDeferredIdentificationVariables() : void
 {
 foreach ($this->deferredIdentificationVariables as $deferredItem) {
 $identVariable = $deferredItem['expression'];
 // Check if IdentificationVariable exists in queryComponents
 if (!isset($this->queryComponents[$identVariable])) {
 $this->semanticalError(sprintf("'%s' is not defined.", $identVariable), $deferredItem['token']);
 }
 $qComp = $this->queryComponents[$identVariable];
 // Check if queryComponent points to an AbstractSchemaName or a ResultVariable
 if (!isset($qComp['metadata'])) {
 $this->semanticalError(sprintf("'%s' does not point to a Class.", $identVariable), $deferredItem['token']);
 }
 // Validate if identification variable nesting level is lower or equal than the current one
 if ($qComp['nestingLevel'] > $deferredItem['nestingLevel']) {
 $this->semanticalError(sprintf("'%s' is used outside the scope of its declaration.", $identVariable), $deferredItem['token']);
 }
 }
 }
 private function processDeferredNewObjectExpressions(AST\SelectStatement $AST) : void
 {
 foreach ($this->deferredNewObjectExpressions as $deferredItem) {
 $expression = $deferredItem['expression'];
 $token = $deferredItem['token'];
 $className = $expression->className;
 $args = $expression->args;
 $fromClassName = $AST->fromClause->identificationVariableDeclarations[0]->rangeVariableDeclaration->abstractSchemaName ?? null;
 // If the namespace is not given then assumes the first FROM entity namespace
 if (!str_contains($className, '\\') && !class_exists($className) && is_string($fromClassName) && str_contains($fromClassName, '\\')) {
 $namespace = substr($fromClassName, 0, strrpos($fromClassName, '\\'));
 $fqcn = $namespace . '\\' . $className;
 if (class_exists($fqcn)) {
 $expression->className = $fqcn;
 $className = $fqcn;
 }
 }
 if (!class_exists($className)) {
 $this->semanticalError(sprintf('Class "%s" is not defined.', $className), $token);
 }
 $class = new ReflectionClass($className);
 if (!$class->isInstantiable()) {
 $this->semanticalError(sprintf('Class "%s" can not be instantiated.', $className), $token);
 }
 if ($class->getConstructor() === null) {
 $this->semanticalError(sprintf('Class "%s" has not a valid constructor.', $className), $token);
 }
 if ($class->getConstructor()->getNumberOfRequiredParameters() > count($args)) {
 $this->semanticalError(sprintf('Number of arguments does not match with "%s" constructor declaration.', $className), $token);
 }
 }
 }
 private function processDeferredPartialObjectExpressions() : void
 {
 foreach ($this->deferredPartialObjectExpressions as $deferredItem) {
 $expr = $deferredItem['expression'];
 $class = $this->getMetadataForDqlAlias($expr->identificationVariable);
 foreach ($expr->partialFieldSet as $field) {
 if (isset($class->fieldMappings[$field])) {
 continue;
 }
 if (isset($class->associationMappings[$field]) && $class->associationMappings[$field]['isOwningSide'] && $class->associationMappings[$field]['type'] & ClassMetadata::TO_ONE) {
 continue;
 }
 $this->semanticalError(sprintf("There is no mapped field named '%s' on class %s.", $field, $class->name), $deferredItem['token']);
 }
 if (array_intersect($class->identifier, $expr->partialFieldSet) !== $class->identifier) {
 $this->semanticalError('The partial field selection of class ' . $class->name . ' must contain the identifier.', $deferredItem['token']);
 }
 }
 }
 private function processDeferredResultVariables() : void
 {
 foreach ($this->deferredResultVariables as $deferredItem) {
 $resultVariable = $deferredItem['expression'];
 // Check if ResultVariable exists in queryComponents
 if (!isset($this->queryComponents[$resultVariable])) {
 $this->semanticalError(sprintf("'%s' is not defined.", $resultVariable), $deferredItem['token']);
 }
 $qComp = $this->queryComponents[$resultVariable];
 // Check if queryComponent points to an AbstractSchemaName or a ResultVariable
 if (!isset($qComp['resultVariable'])) {
 $this->semanticalError(sprintf("'%s' does not point to a ResultVariable.", $resultVariable), $deferredItem['token']);
 }
 // Validate if identification variable nesting level is lower or equal than the current one
 if ($qComp['nestingLevel'] > $deferredItem['nestingLevel']) {
 $this->semanticalError(sprintf("'%s' is used outside the scope of its declaration.", $resultVariable), $deferredItem['token']);
 }
 }
 }
 private function processDeferredPathExpressions() : void
 {
 foreach ($this->deferredPathExpressions as $deferredItem) {
 $pathExpression = $deferredItem['expression'];
 $class = $this->getMetadataForDqlAlias($pathExpression->identificationVariable);
 $field = $pathExpression->field;
 if ($field === null) {
 $field = $pathExpression->field = $class->identifier[0];
 }
 // Check if field or association exists
 if (!isset($class->associationMappings[$field]) && !isset($class->fieldMappings[$field])) {
 $this->semanticalError('Class ' . $class->name . ' has no field or association named ' . $field, $deferredItem['token']);
 }
 $fieldType = AST\PathExpression::TYPE_STATE_FIELD;
 if (isset($class->associationMappings[$field])) {
 $assoc = $class->associationMappings[$field];
 $fieldType = $assoc['type'] & ClassMetadata::TO_ONE ? AST\PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION : AST\PathExpression::TYPE_COLLECTION_VALUED_ASSOCIATION;
 }
 // Validate if PathExpression is one of the expected types
 $expectedType = $pathExpression->expectedType;
 if (!($expectedType & $fieldType)) {
 // We need to recognize which was expected type(s)
 $expectedStringTypes = [];
 // Validate state field type
 if ($expectedType & AST\PathExpression::TYPE_STATE_FIELD) {
 $expectedStringTypes[] = 'StateFieldPathExpression';
 }
 // Validate single valued association (*-to-one)
 if ($expectedType & AST\PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION) {
 $expectedStringTypes[] = 'SingleValuedAssociationField';
 }
 // Validate single valued association (*-to-many)
 if ($expectedType & AST\PathExpression::TYPE_COLLECTION_VALUED_ASSOCIATION) {
 $expectedStringTypes[] = 'CollectionValuedAssociationField';
 }
 // Build the error message
 $semanticalError = 'Invalid PathExpression. ';
 $semanticalError .= count($expectedStringTypes) === 1 ? 'Must be a ' . $expectedStringTypes[0] . '.' : implode(' or ', $expectedStringTypes) . ' expected.';
 $this->semanticalError($semanticalError, $deferredItem['token']);
 }
 // We need to force the type in PathExpression
 $pathExpression->type = $fieldType;
 }
 }
 private function processRootEntityAliasSelected() : void
 {
 if (!count($this->identVariableExpressions)) {
 return;
 }
 foreach ($this->identVariableExpressions as $dqlAlias => $expr) {
 if (isset($this->queryComponents[$dqlAlias]) && !isset($this->queryComponents[$dqlAlias]['parent'])) {
 return;
 }
 }
 $this->semanticalError('Cannot select entity through identification variables without choosing at least one root entity alias.');
 }
 public function QueryLanguage()
 {
 $statement = null;
 $this->lexer->moveNext();
 switch ($this->lexer->lookahead['type'] ?? null) {
 case Lexer::T_SELECT:
 $statement = $this->SelectStatement();
 break;
 case Lexer::T_UPDATE:
 $statement = $this->UpdateStatement();
 break;
 case Lexer::T_DELETE:
 $statement = $this->DeleteStatement();
 break;
 default:
 $this->syntaxError('SELECT, UPDATE or DELETE');
 break;
 }
 // Check for end of string
 if ($this->lexer->lookahead !== null) {
 $this->syntaxError('end of string');
 }
 return $statement;
 }
 public function SelectStatement()
 {
 $selectStatement = new AST\SelectStatement($this->SelectClause(), $this->FromClause());
 $selectStatement->whereClause = $this->lexer->isNextToken(Lexer::T_WHERE) ? $this->WhereClause() : null;
 $selectStatement->groupByClause = $this->lexer->isNextToken(Lexer::T_GROUP) ? $this->GroupByClause() : null;
 $selectStatement->havingClause = $this->lexer->isNextToken(Lexer::T_HAVING) ? $this->HavingClause() : null;
 $selectStatement->orderByClause = $this->lexer->isNextToken(Lexer::T_ORDER) ? $this->OrderByClause() : null;
 return $selectStatement;
 }
 public function UpdateStatement()
 {
 $updateStatement = new AST\UpdateStatement($this->UpdateClause());
 $updateStatement->whereClause = $this->lexer->isNextToken(Lexer::T_WHERE) ? $this->WhereClause() : null;
 return $updateStatement;
 }
 public function DeleteStatement()
 {
 $deleteStatement = new AST\DeleteStatement($this->DeleteClause());
 $deleteStatement->whereClause = $this->lexer->isNextToken(Lexer::T_WHERE) ? $this->WhereClause() : null;
 return $deleteStatement;
 }
 public function IdentificationVariable()
 {
 $this->match(Lexer::T_IDENTIFIER);
 assert($this->lexer->token !== null);
 $identVariable = $this->lexer->token['value'];
 $this->deferredIdentificationVariables[] = ['expression' => $identVariable, 'nestingLevel' => $this->nestingLevel, 'token' => $this->lexer->token];
 return $identVariable;
 }
 public function AliasIdentificationVariable()
 {
 $this->match(Lexer::T_IDENTIFIER);
 assert($this->lexer->token !== null);
 $aliasIdentVariable = $this->lexer->token['value'];
 $exists = isset($this->queryComponents[$aliasIdentVariable]);
 if ($exists) {
 $this->semanticalError(sprintf("'%s' is already defined.", $aliasIdentVariable), $this->lexer->token);
 }
 return $aliasIdentVariable;
 }
 public function AbstractSchemaName()
 {
 if ($this->lexer->isNextToken(Lexer::T_FULLY_QUALIFIED_NAME)) {
 $this->match(Lexer::T_FULLY_QUALIFIED_NAME);
 assert($this->lexer->token !== null);
 return $this->lexer->token['value'];
 }
 if ($this->lexer->isNextToken(Lexer::T_IDENTIFIER)) {
 $this->match(Lexer::T_IDENTIFIER);
 assert($this->lexer->token !== null);
 return $this->lexer->token['value'];
 }
 $this->match(Lexer::T_ALIASED_NAME);
 assert($this->lexer->token !== null);
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8818', 'Short namespace aliases such as "%s" are deprecated and will be removed in Doctrine ORM 3.0.', $this->lexer->token['value']);
 [$namespaceAlias, $simpleClassName] = explode(':', $this->lexer->token['value']);
 return $this->em->getConfiguration()->getEntityNamespace($namespaceAlias) . '\\' . $simpleClassName;
 }
 private function validateAbstractSchemaName(string $schemaName) : void
 {
 assert($this->lexer->token !== null);
 if (!(class_exists($schemaName, \true) || interface_exists($schemaName, \true))) {
 $this->semanticalError(sprintf("Class '%s' is not defined.", $schemaName), $this->lexer->token);
 }
 }
 public function AliasResultVariable()
 {
 $this->match(Lexer::T_IDENTIFIER);
 assert($this->lexer->token !== null);
 $resultVariable = $this->lexer->token['value'];
 $exists = isset($this->queryComponents[$resultVariable]);
 if ($exists) {
 $this->semanticalError(sprintf("'%s' is already defined.", $resultVariable), $this->lexer->token);
 }
 return $resultVariable;
 }
 public function ResultVariable()
 {
 $this->match(Lexer::T_IDENTIFIER);
 assert($this->lexer->token !== null);
 $resultVariable = $this->lexer->token['value'];
 // Defer ResultVariable validation
 $this->deferredResultVariables[] = ['expression' => $resultVariable, 'nestingLevel' => $this->nestingLevel, 'token' => $this->lexer->token];
 return $resultVariable;
 }
 public function JoinAssociationPathExpression()
 {
 $identVariable = $this->IdentificationVariable();
 if (!isset($this->queryComponents[$identVariable])) {
 $this->semanticalError('Identification Variable ' . $identVariable . ' used in join path expression but was not defined before.');
 }
 $this->match(Lexer::T_DOT);
 $this->match(Lexer::T_IDENTIFIER);
 assert($this->lexer->token !== null);
 $field = $this->lexer->token['value'];
 // Validate association field
 $class = $this->getMetadataForDqlAlias($identVariable);
 if (!$class->hasAssociation($field)) {
 $this->semanticalError('Class ' . $class->name . ' has no association named ' . $field);
 }
 return new AST\JoinAssociationPathExpression($identVariable, $field);
 }
 public function PathExpression($expectedTypes)
 {
 $identVariable = $this->IdentificationVariable();
 $field = null;
 assert($this->lexer->token !== null);
 if ($this->lexer->isNextToken(Lexer::T_DOT)) {
 $this->match(Lexer::T_DOT);
 $this->match(Lexer::T_IDENTIFIER);
 $field = $this->lexer->token['value'];
 while ($this->lexer->isNextToken(Lexer::T_DOT)) {
 $this->match(Lexer::T_DOT);
 $this->match(Lexer::T_IDENTIFIER);
 $field .= '.' . $this->lexer->token['value'];
 }
 }
 // Creating AST node
 $pathExpr = new AST\PathExpression($expectedTypes, $identVariable, $field);
 // Defer PathExpression validation if requested to be deferred
 $this->deferredPathExpressions[] = ['expression' => $pathExpr, 'nestingLevel' => $this->nestingLevel, 'token' => $this->lexer->token];
 return $pathExpr;
 }
 public function AssociationPathExpression()
 {
 return $this->PathExpression(AST\PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION | AST\PathExpression::TYPE_COLLECTION_VALUED_ASSOCIATION);
 }
 public function SingleValuedPathExpression()
 {
 return $this->PathExpression(AST\PathExpression::TYPE_STATE_FIELD | AST\PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION);
 }
 public function StateFieldPathExpression()
 {
 return $this->PathExpression(AST\PathExpression::TYPE_STATE_FIELD);
 }
 public function SingleValuedAssociationPathExpression()
 {
 return $this->PathExpression(AST\PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION);
 }
 public function CollectionValuedPathExpression()
 {
 return $this->PathExpression(AST\PathExpression::TYPE_COLLECTION_VALUED_ASSOCIATION);
 }
 public function SelectClause()
 {
 $isDistinct = \false;
 $this->match(Lexer::T_SELECT);
 // Check for DISTINCT
 if ($this->lexer->isNextToken(Lexer::T_DISTINCT)) {
 $this->match(Lexer::T_DISTINCT);
 $isDistinct = \true;
 }
 // Process SelectExpressions (1..N)
 $selectExpressions = [];
 $selectExpressions[] = $this->SelectExpression();
 while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
 $this->match(Lexer::T_COMMA);
 $selectExpressions[] = $this->SelectExpression();
 }
 return new AST\SelectClause($selectExpressions, $isDistinct);
 }
 public function SimpleSelectClause()
 {
 $isDistinct = \false;
 $this->match(Lexer::T_SELECT);
 if ($this->lexer->isNextToken(Lexer::T_DISTINCT)) {
 $this->match(Lexer::T_DISTINCT);
 $isDistinct = \true;
 }
 return new AST\SimpleSelectClause($this->SimpleSelectExpression(), $isDistinct);
 }
 public function UpdateClause()
 {
 $this->match(Lexer::T_UPDATE);
 assert($this->lexer->lookahead !== null);
 $token = $this->lexer->lookahead;
 $abstractSchemaName = $this->AbstractSchemaName();
 $this->validateAbstractSchemaName($abstractSchemaName);
 if ($this->lexer->isNextToken(Lexer::T_AS)) {
 $this->match(Lexer::T_AS);
 }
 $aliasIdentificationVariable = $this->AliasIdentificationVariable();
 $class = $this->em->getClassMetadata($abstractSchemaName);
 // Building queryComponent
 $queryComponent = ['metadata' => $class, 'parent' => null, 'relation' => null, 'map' => null, 'nestingLevel' => $this->nestingLevel, 'token' => $token];
 $this->queryComponents[$aliasIdentificationVariable] = $queryComponent;
 $this->match(Lexer::T_SET);
 $updateItems = [];
 $updateItems[] = $this->UpdateItem();
 while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
 $this->match(Lexer::T_COMMA);
 $updateItems[] = $this->UpdateItem();
 }
 $updateClause = new AST\UpdateClause($abstractSchemaName, $updateItems);
 $updateClause->aliasIdentificationVariable = $aliasIdentificationVariable;
 return $updateClause;
 }
 public function DeleteClause()
 {
 $this->match(Lexer::T_DELETE);
 if ($this->lexer->isNextToken(Lexer::T_FROM)) {
 $this->match(Lexer::T_FROM);
 }
 assert($this->lexer->lookahead !== null);
 $token = $this->lexer->lookahead;
 $abstractSchemaName = $this->AbstractSchemaName();
 $this->validateAbstractSchemaName($abstractSchemaName);
 $deleteClause = new AST\DeleteClause($abstractSchemaName);
 if ($this->lexer->isNextToken(Lexer::T_AS)) {
 $this->match(Lexer::T_AS);
 }
 $aliasIdentificationVariable = $this->lexer->isNextToken(Lexer::T_IDENTIFIER) ? $this->AliasIdentificationVariable() : 'alias_should_have_been_set';
 $deleteClause->aliasIdentificationVariable = $aliasIdentificationVariable;
 $class = $this->em->getClassMetadata($deleteClause->abstractSchemaName);
 // Building queryComponent
 $queryComponent = ['metadata' => $class, 'parent' => null, 'relation' => null, 'map' => null, 'nestingLevel' => $this->nestingLevel, 'token' => $token];
 $this->queryComponents[$aliasIdentificationVariable] = $queryComponent;
 return $deleteClause;
 }
 public function FromClause()
 {
 $this->match(Lexer::T_FROM);
 $identificationVariableDeclarations = [];
 $identificationVariableDeclarations[] = $this->IdentificationVariableDeclaration();
 while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
 $this->match(Lexer::T_COMMA);
 $identificationVariableDeclarations[] = $this->IdentificationVariableDeclaration();
 }
 return new AST\FromClause($identificationVariableDeclarations);
 }
 public function SubselectFromClause()
 {
 $this->match(Lexer::T_FROM);
 $identificationVariables = [];
 $identificationVariables[] = $this->SubselectIdentificationVariableDeclaration();
 while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
 $this->match(Lexer::T_COMMA);
 $identificationVariables[] = $this->SubselectIdentificationVariableDeclaration();
 }
 return new AST\SubselectFromClause($identificationVariables);
 }
 public function WhereClause()
 {
 $this->match(Lexer::T_WHERE);
 return new AST\WhereClause($this->ConditionalExpression());
 }
 public function HavingClause()
 {
 $this->match(Lexer::T_HAVING);
 return new AST\HavingClause($this->ConditionalExpression());
 }
 public function GroupByClause()
 {
 $this->match(Lexer::T_GROUP);
 $this->match(Lexer::T_BY);
 $groupByItems = [$this->GroupByItem()];
 while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
 $this->match(Lexer::T_COMMA);
 $groupByItems[] = $this->GroupByItem();
 }
 return new AST\GroupByClause($groupByItems);
 }
 public function OrderByClause()
 {
 $this->match(Lexer::T_ORDER);
 $this->match(Lexer::T_BY);
 $orderByItems = [];
 $orderByItems[] = $this->OrderByItem();
 while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
 $this->match(Lexer::T_COMMA);
 $orderByItems[] = $this->OrderByItem();
 }
 return new AST\OrderByClause($orderByItems);
 }
 public function Subselect()
 {
 // Increase query nesting level
 $this->nestingLevel++;
 $subselect = new AST\Subselect($this->SimpleSelectClause(), $this->SubselectFromClause());
 $subselect->whereClause = $this->lexer->isNextToken(Lexer::T_WHERE) ? $this->WhereClause() : null;
 $subselect->groupByClause = $this->lexer->isNextToken(Lexer::T_GROUP) ? $this->GroupByClause() : null;
 $subselect->havingClause = $this->lexer->isNextToken(Lexer::T_HAVING) ? $this->HavingClause() : null;
 $subselect->orderByClause = $this->lexer->isNextToken(Lexer::T_ORDER) ? $this->OrderByClause() : null;
 // Decrease query nesting level
 $this->nestingLevel--;
 return $subselect;
 }
 public function UpdateItem()
 {
 $pathExpr = $this->SingleValuedPathExpression();
 $this->match(Lexer::T_EQUALS);
 return new AST\UpdateItem($pathExpr, $this->NewValue());
 }
 public function GroupByItem()
 {
 // We need to check if we are in a IdentificationVariable or SingleValuedPathExpression
 $glimpse = $this->lexer->glimpse();
 if ($glimpse !== null && $glimpse['type'] === Lexer::T_DOT) {
 return $this->SingleValuedPathExpression();
 }
 assert($this->lexer->lookahead !== null);
 // Still need to decide between IdentificationVariable or ResultVariable
 $lookaheadValue = $this->lexer->lookahead['value'];
 if (!isset($this->queryComponents[$lookaheadValue])) {
 $this->semanticalError('Cannot group by undefined identification or result variable.');
 }
 return isset($this->queryComponents[$lookaheadValue]['metadata']) ? $this->IdentificationVariable() : $this->ResultVariable();
 }
 public function OrderByItem()
 {
 $this->lexer->peek();
 // lookahead => '.'
 $this->lexer->peek();
 // lookahead => token after '.'
 $peek = $this->lexer->peek();
 // lookahead => token after the token after the '.'
 $this->lexer->resetPeek();
 $glimpse = $this->lexer->glimpse();
 assert($this->lexer->lookahead !== null);
 switch (\true) {
 case $this->isMathOperator($peek):
 $expr = $this->SimpleArithmeticExpression();
 break;
 case $glimpse !== null && $glimpse['type'] === Lexer::T_DOT:
 $expr = $this->SingleValuedPathExpression();
 break;
 case $this->lexer->peek() && $this->isMathOperator($this->peekBeyondClosingParenthesis()):
 $expr = $this->ScalarExpression();
 break;
 case $this->lexer->lookahead['type'] === Lexer::T_CASE:
 $expr = $this->CaseExpression();
 break;
 case $this->isFunction():
 $expr = $this->FunctionDeclaration();
 break;
 default:
 $expr = $this->ResultVariable();
 break;
 }
 $type = 'ASC';
 $item = new AST\OrderByItem($expr);
 switch (\true) {
 case $this->lexer->isNextToken(Lexer::T_DESC):
 $this->match(Lexer::T_DESC);
 $type = 'DESC';
 break;
 case $this->lexer->isNextToken(Lexer::T_ASC):
 $this->match(Lexer::T_ASC);
 break;
 default:
 }
 $item->type = $type;
 return $item;
 }
 public function NewValue()
 {
 if ($this->lexer->isNextToken(Lexer::T_NULL)) {
 $this->match(Lexer::T_NULL);
 return null;
 }
 if ($this->lexer->isNextToken(Lexer::T_INPUT_PARAMETER)) {
 $this->match(Lexer::T_INPUT_PARAMETER);
 assert($this->lexer->token !== null);
 return new AST\InputParameter($this->lexer->token['value']);
 }
 return $this->ArithmeticExpression();
 }
 public function IdentificationVariableDeclaration()
 {
 $joins = [];
 $rangeVariableDeclaration = $this->RangeVariableDeclaration();
 $indexBy = $this->lexer->isNextToken(Lexer::T_INDEX) ? $this->IndexBy() : null;
 $rangeVariableDeclaration->isRoot = \true;
 while ($this->lexer->isNextToken(Lexer::T_LEFT) || $this->lexer->isNextToken(Lexer::T_INNER) || $this->lexer->isNextToken(Lexer::T_JOIN)) {
 $joins[] = $this->Join();
 }
 return new AST\IdentificationVariableDeclaration($rangeVariableDeclaration, $indexBy, $joins);
 }
 public function SubselectIdentificationVariableDeclaration()
 {
 return $this->IdentificationVariableDeclaration();
 }
 public function Join()
 {
 // Check Join type
 $joinType = AST\Join::JOIN_TYPE_INNER;
 switch (\true) {
 case $this->lexer->isNextToken(Lexer::T_LEFT):
 $this->match(Lexer::T_LEFT);
 $joinType = AST\Join::JOIN_TYPE_LEFT;
 // Possible LEFT OUTER join
 if ($this->lexer->isNextToken(Lexer::T_OUTER)) {
 $this->match(Lexer::T_OUTER);
 $joinType = AST\Join::JOIN_TYPE_LEFTOUTER;
 }
 break;
 case $this->lexer->isNextToken(Lexer::T_INNER):
 $this->match(Lexer::T_INNER);
 break;
 default:
 }
 $this->match(Lexer::T_JOIN);
 $next = $this->lexer->glimpse();
 assert($next !== null);
 $joinDeclaration = $next['type'] === Lexer::T_DOT ? $this->JoinAssociationDeclaration() : $this->RangeVariableDeclaration();
 $adhocConditions = $this->lexer->isNextToken(Lexer::T_WITH);
 $join = new AST\Join($joinType, $joinDeclaration);
 // Describe non-root join declaration
 if ($joinDeclaration instanceof AST\RangeVariableDeclaration) {
 $joinDeclaration->isRoot = \false;
 }
 // Check for ad-hoc Join conditions
 if ($adhocConditions) {
 $this->match(Lexer::T_WITH);
 $join->conditionalExpression = $this->ConditionalExpression();
 }
 return $join;
 }
 public function RangeVariableDeclaration()
 {
 if ($this->lexer->isNextToken(Lexer::T_OPEN_PARENTHESIS) && $this->lexer->glimpse()['type'] === Lexer::T_SELECT) {
 $this->semanticalError('Subquery is not supported here', $this->lexer->token);
 }
 $abstractSchemaName = $this->AbstractSchemaName();
 $this->validateAbstractSchemaName($abstractSchemaName);
 if ($this->lexer->isNextToken(Lexer::T_AS)) {
 $this->match(Lexer::T_AS);
 }
 assert($this->lexer->lookahead !== null);
 $token = $this->lexer->lookahead;
 $aliasIdentificationVariable = $this->AliasIdentificationVariable();
 $classMetadata = $this->em->getClassMetadata($abstractSchemaName);
 // Building queryComponent
 $queryComponent = ['metadata' => $classMetadata, 'parent' => null, 'relation' => null, 'map' => null, 'nestingLevel' => $this->nestingLevel, 'token' => $token];
 $this->queryComponents[$aliasIdentificationVariable] = $queryComponent;
 return new AST\RangeVariableDeclaration($abstractSchemaName, $aliasIdentificationVariable);
 }
 public function JoinAssociationDeclaration()
 {
 $joinAssociationPathExpression = $this->JoinAssociationPathExpression();
 if ($this->lexer->isNextToken(Lexer::T_AS)) {
 $this->match(Lexer::T_AS);
 }
 assert($this->lexer->lookahead !== null);
 $aliasIdentificationVariable = $this->AliasIdentificationVariable();
 $indexBy = $this->lexer->isNextToken(Lexer::T_INDEX) ? $this->IndexBy() : null;
 $identificationVariable = $joinAssociationPathExpression->identificationVariable;
 $field = $joinAssociationPathExpression->associationField;
 $class = $this->getMetadataForDqlAlias($identificationVariable);
 $targetClass = $this->em->getClassMetadata($class->associationMappings[$field]['targetEntity']);
 // Building queryComponent
 $joinQueryComponent = ['metadata' => $targetClass, 'parent' => $joinAssociationPathExpression->identificationVariable, 'relation' => $class->getAssociationMapping($field), 'map' => null, 'nestingLevel' => $this->nestingLevel, 'token' => $this->lexer->lookahead];
 $this->queryComponents[$aliasIdentificationVariable] = $joinQueryComponent;
 return new AST\JoinAssociationDeclaration($joinAssociationPathExpression, $aliasIdentificationVariable, $indexBy);
 }
 public function PartialObjectExpression()
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8471', 'PARTIAL syntax in DQL is deprecated.');
 $this->match(Lexer::T_PARTIAL);
 $partialFieldSet = [];
 $identificationVariable = $this->IdentificationVariable();
 $this->match(Lexer::T_DOT);
 $this->match(Lexer::T_OPEN_CURLY_BRACE);
 $this->match(Lexer::T_IDENTIFIER);
 assert($this->lexer->token !== null);
 $field = $this->lexer->token['value'];
 // First field in partial expression might be embeddable property
 while ($this->lexer->isNextToken(Lexer::T_DOT)) {
 $this->match(Lexer::T_DOT);
 $this->match(Lexer::T_IDENTIFIER);
 $field .= '.' . $this->lexer->token['value'];
 }
 $partialFieldSet[] = $field;
 while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
 $this->match(Lexer::T_COMMA);
 $this->match(Lexer::T_IDENTIFIER);
 $field = $this->lexer->token['value'];
 while ($this->lexer->isNextToken(Lexer::T_DOT)) {
 $this->match(Lexer::T_DOT);
 $this->match(Lexer::T_IDENTIFIER);
 $field .= '.' . $this->lexer->token['value'];
 }
 $partialFieldSet[] = $field;
 }
 $this->match(Lexer::T_CLOSE_CURLY_BRACE);
 $partialObjectExpression = new AST\PartialObjectExpression($identificationVariable, $partialFieldSet);
 // Defer PartialObjectExpression validation
 $this->deferredPartialObjectExpressions[] = ['expression' => $partialObjectExpression, 'nestingLevel' => $this->nestingLevel, 'token' => $this->lexer->token];
 return $partialObjectExpression;
 }
 public function NewObjectExpression()
 {
 $this->match(Lexer::T_NEW);
 $className = $this->AbstractSchemaName();
 // note that this is not yet validated
 $token = $this->lexer->token;
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $args[] = $this->NewObjectArg();
 while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
 $this->match(Lexer::T_COMMA);
 $args[] = $this->NewObjectArg();
 }
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 $expression = new AST\NewObjectExpression($className, $args);
 // Defer NewObjectExpression validation
 $this->deferredNewObjectExpressions[] = ['token' => $token, 'expression' => $expression, 'nestingLevel' => $this->nestingLevel];
 return $expression;
 }
 public function NewObjectArg()
 {
 assert($this->lexer->lookahead !== null);
 $token = $this->lexer->lookahead;
 $peek = $this->lexer->glimpse();
 assert($peek !== null);
 if ($token['type'] === Lexer::T_OPEN_PARENTHESIS && $peek['type'] === Lexer::T_SELECT) {
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $expression = $this->Subselect();
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return $expression;
 }
 return $this->ScalarExpression();
 }
 public function IndexBy()
 {
 $this->match(Lexer::T_INDEX);
 $this->match(Lexer::T_BY);
 $pathExpr = $this->SingleValuedPathExpression();
 // Add the INDEX BY info to the query component
 $this->queryComponents[$pathExpr->identificationVariable]['map'] = $pathExpr->field;
 return new AST\IndexBy($pathExpr);
 }
 public function ScalarExpression()
 {
 assert($this->lexer->token !== null);
 assert($this->lexer->lookahead !== null);
 $lookahead = $this->lexer->lookahead['type'];
 $peek = $this->lexer->glimpse();
 switch (\true) {
 case $lookahead === Lexer::T_INTEGER:
 case $lookahead === Lexer::T_FLOAT:
 // SimpleArithmeticExpression : (- u.value ) or ( + u.value ) or ( - 1 ) or ( + 1 )
 case $lookahead === Lexer::T_MINUS:
 case $lookahead === Lexer::T_PLUS:
 return $this->SimpleArithmeticExpression();
 case $lookahead === Lexer::T_STRING:
 return $this->StringPrimary();
 case $lookahead === Lexer::T_TRUE:
 case $lookahead === Lexer::T_FALSE:
 $this->match($lookahead);
 return new AST\Literal(AST\Literal::BOOLEAN, $this->lexer->token['value']);
 case $lookahead === Lexer::T_INPUT_PARAMETER:
 switch (\true) {
 case $this->isMathOperator($peek):
 // :param + u.value
 return $this->SimpleArithmeticExpression();
 default:
 return $this->InputParameter();
 }
 case $lookahead === Lexer::T_CASE:
 case $lookahead === Lexer::T_COALESCE:
 case $lookahead === Lexer::T_NULLIF:
 // Since NULLIF and COALESCE can be identified as a function,
 // we need to check these before checking for FunctionDeclaration
 return $this->CaseExpression();
 case $lookahead === Lexer::T_OPEN_PARENTHESIS:
 return $this->SimpleArithmeticExpression();
 // this check must be done before checking for a filed path expression
 case $this->isFunction():
 $this->lexer->peek();
 // "("
 switch (\true) {
 case $this->isMathOperator($this->peekBeyondClosingParenthesis()):
 // SUM(u.id) + COUNT(u.id)
 return $this->SimpleArithmeticExpression();
 default:
 // IDENTITY(u)
 return $this->FunctionDeclaration();
 }
 break;
 // it is no function, so it must be a field path
 case $lookahead === Lexer::T_IDENTIFIER:
 $this->lexer->peek();
 // lookahead => '.'
 $this->lexer->peek();
 // lookahead => token after '.'
 $peek = $this->lexer->peek();
 // lookahead => token after the token after the '.'
 $this->lexer->resetPeek();
 if ($this->isMathOperator($peek)) {
 return $this->SimpleArithmeticExpression();
 }
 return $this->StateFieldPathExpression();
 default:
 $this->syntaxError();
 }
 }
 public function CaseExpression()
 {
 assert($this->lexer->lookahead !== null);
 $lookahead = $this->lexer->lookahead['type'];
 switch ($lookahead) {
 case Lexer::T_NULLIF:
 return $this->NullIfExpression();
 case Lexer::T_COALESCE:
 return $this->CoalesceExpression();
 case Lexer::T_CASE:
 $this->lexer->resetPeek();
 $peek = $this->lexer->peek();
 assert($peek !== null);
 if ($peek['type'] === Lexer::T_WHEN) {
 return $this->GeneralCaseExpression();
 }
 return $this->SimpleCaseExpression();
 default:
 // Do nothing
 break;
 }
 $this->syntaxError();
 }
 public function CoalesceExpression()
 {
 $this->match(Lexer::T_COALESCE);
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 // Process ScalarExpressions (1..N)
 $scalarExpressions = [];
 $scalarExpressions[] = $this->ScalarExpression();
 while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
 $this->match(Lexer::T_COMMA);
 $scalarExpressions[] = $this->ScalarExpression();
 }
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return new AST\CoalesceExpression($scalarExpressions);
 }
 public function NullIfExpression()
 {
 $this->match(Lexer::T_NULLIF);
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $firstExpression = $this->ScalarExpression();
 $this->match(Lexer::T_COMMA);
 $secondExpression = $this->ScalarExpression();
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return new AST\NullIfExpression($firstExpression, $secondExpression);
 }
 public function GeneralCaseExpression()
 {
 $this->match(Lexer::T_CASE);
 // Process WhenClause (1..N)
 $whenClauses = [];
 do {
 $whenClauses[] = $this->WhenClause();
 } while ($this->lexer->isNextToken(Lexer::T_WHEN));
 $this->match(Lexer::T_ELSE);
 $scalarExpression = $this->ScalarExpression();
 $this->match(Lexer::T_END);
 return new AST\GeneralCaseExpression($whenClauses, $scalarExpression);
 }
 public function SimpleCaseExpression()
 {
 $this->match(Lexer::T_CASE);
 $caseOperand = $this->StateFieldPathExpression();
 // Process SimpleWhenClause (1..N)
 $simpleWhenClauses = [];
 do {
 $simpleWhenClauses[] = $this->SimpleWhenClause();
 } while ($this->lexer->isNextToken(Lexer::T_WHEN));
 $this->match(Lexer::T_ELSE);
 $scalarExpression = $this->ScalarExpression();
 $this->match(Lexer::T_END);
 return new AST\SimpleCaseExpression($caseOperand, $simpleWhenClauses, $scalarExpression);
 }
 public function WhenClause()
 {
 $this->match(Lexer::T_WHEN);
 $conditionalExpression = $this->ConditionalExpression();
 $this->match(Lexer::T_THEN);
 return new AST\WhenClause($conditionalExpression, $this->ScalarExpression());
 }
 public function SimpleWhenClause()
 {
 $this->match(Lexer::T_WHEN);
 $conditionalExpression = $this->ScalarExpression();
 $this->match(Lexer::T_THEN);
 return new AST\SimpleWhenClause($conditionalExpression, $this->ScalarExpression());
 }
 public function SelectExpression()
 {
 assert($this->lexer->lookahead !== null);
 $expression = null;
 $identVariable = null;
 $peek = $this->lexer->glimpse();
 $lookaheadType = $this->lexer->lookahead['type'];
 assert($peek !== null);
 switch (\true) {
 // ScalarExpression (u.name)
 case $lookaheadType === Lexer::T_IDENTIFIER && $peek['type'] === Lexer::T_DOT:
 $expression = $this->ScalarExpression();
 break;
 // IdentificationVariable (u)
 case $lookaheadType === Lexer::T_IDENTIFIER && $peek['type'] !== Lexer::T_OPEN_PARENTHESIS:
 $expression = $identVariable = $this->IdentificationVariable();
 break;
 // CaseExpression (CASE ... or NULLIF(...) or COALESCE(...))
 case $lookaheadType === Lexer::T_CASE:
 case $lookaheadType === Lexer::T_COALESCE:
 case $lookaheadType === Lexer::T_NULLIF:
 $expression = $this->CaseExpression();
 break;
 // DQL Function (SUM(u.value) or SUM(u.value) + 1)
 case $this->isFunction():
 $this->lexer->peek();
 // "("
 switch (\true) {
 case $this->isMathOperator($this->peekBeyondClosingParenthesis()):
 // SUM(u.id) + COUNT(u.id)
 $expression = $this->ScalarExpression();
 break;
 default:
 // IDENTITY(u)
 $expression = $this->FunctionDeclaration();
 break;
 }
 break;
 // PartialObjectExpression (PARTIAL u.{id, name})
 case $lookaheadType === Lexer::T_PARTIAL:
 $expression = $this->PartialObjectExpression();
 $identVariable = $expression->identificationVariable;
 break;
 // Subselect
 case $lookaheadType === Lexer::T_OPEN_PARENTHESIS && $peek['type'] === Lexer::T_SELECT:
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $expression = $this->Subselect();
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 break;
 // Shortcut: ScalarExpression => SimpleArithmeticExpression
 case $lookaheadType === Lexer::T_OPEN_PARENTHESIS:
 case $lookaheadType === Lexer::T_INTEGER:
 case $lookaheadType === Lexer::T_STRING:
 case $lookaheadType === Lexer::T_FLOAT:
 // SimpleArithmeticExpression : (- u.value ) or ( + u.value )
 case $lookaheadType === Lexer::T_MINUS:
 case $lookaheadType === Lexer::T_PLUS:
 $expression = $this->SimpleArithmeticExpression();
 break;
 // NewObjectExpression (New ClassName(id, name))
 case $lookaheadType === Lexer::T_NEW:
 $expression = $this->NewObjectExpression();
 break;
 default:
 $this->syntaxError('IdentificationVariable | ScalarExpression | AggregateExpression | FunctionDeclaration | PartialObjectExpression | "(" Subselect ")" | CaseExpression', $this->lexer->lookahead);
 }
 // [["AS"] ["HIDDEN"] AliasResultVariable]
 $mustHaveAliasResultVariable = \false;
 if ($this->lexer->isNextToken(Lexer::T_AS)) {
 $this->match(Lexer::T_AS);
 $mustHaveAliasResultVariable = \true;
 }
 $hiddenAliasResultVariable = \false;
 if ($this->lexer->isNextToken(Lexer::T_HIDDEN)) {
 $this->match(Lexer::T_HIDDEN);
 $hiddenAliasResultVariable = \true;
 }
 $aliasResultVariable = null;
 if ($mustHaveAliasResultVariable || $this->lexer->isNextToken(Lexer::T_IDENTIFIER)) {
 assert($expression instanceof AST\Node || is_string($expression));
 $token = $this->lexer->lookahead;
 $aliasResultVariable = $this->AliasResultVariable();
 // Include AliasResultVariable in query components.
 $this->queryComponents[$aliasResultVariable] = ['resultVariable' => $expression, 'nestingLevel' => $this->nestingLevel, 'token' => $token];
 }
 // AST
 $expr = new AST\SelectExpression($expression, $aliasResultVariable, $hiddenAliasResultVariable);
 if ($identVariable) {
 $this->identVariableExpressions[$identVariable] = $expr;
 }
 return $expr;
 }
 public function SimpleSelectExpression()
 {
 assert($this->lexer->lookahead !== null);
 $peek = $this->lexer->glimpse();
 assert($peek !== null);
 switch ($this->lexer->lookahead['type']) {
 case Lexer::T_IDENTIFIER:
 switch (\true) {
 case $peek['type'] === Lexer::T_DOT:
 $expression = $this->StateFieldPathExpression();
 return new AST\SimpleSelectExpression($expression);
 case $peek['type'] !== Lexer::T_OPEN_PARENTHESIS:
 $expression = $this->IdentificationVariable();
 return new AST\SimpleSelectExpression($expression);
 case $this->isFunction():
 // SUM(u.id) + COUNT(u.id)
 if ($this->isMathOperator($this->peekBeyondClosingParenthesis())) {
 return new AST\SimpleSelectExpression($this->ScalarExpression());
 }
 // COUNT(u.id)
 if ($this->isAggregateFunction($this->lexer->lookahead['type'])) {
 return new AST\SimpleSelectExpression($this->AggregateExpression());
 }
 // IDENTITY(u)
 return new AST\SimpleSelectExpression($this->FunctionDeclaration());
 default:
 }
 break;
 case Lexer::T_OPEN_PARENTHESIS:
 if ($peek['type'] !== Lexer::T_SELECT) {
 // Shortcut: ScalarExpression => SimpleArithmeticExpression
 $expression = $this->SimpleArithmeticExpression();
 return new AST\SimpleSelectExpression($expression);
 }
 // Subselect
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $expression = $this->Subselect();
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return new AST\SimpleSelectExpression($expression);
 default:
 }
 $this->lexer->peek();
 $expression = $this->ScalarExpression();
 $expr = new AST\SimpleSelectExpression($expression);
 if ($this->lexer->isNextToken(Lexer::T_AS)) {
 $this->match(Lexer::T_AS);
 }
 if ($this->lexer->isNextToken(Lexer::T_IDENTIFIER)) {
 $token = $this->lexer->lookahead;
 $resultVariable = $this->AliasResultVariable();
 $expr->fieldIdentificationVariable = $resultVariable;
 // Include AliasResultVariable in query components.
 $this->queryComponents[$resultVariable] = ['resultvariable' => $expr, 'nestingLevel' => $this->nestingLevel, 'token' => $token];
 }
 return $expr;
 }
 public function ConditionalExpression()
 {
 $conditionalTerms = [];
 $conditionalTerms[] = $this->ConditionalTerm();
 while ($this->lexer->isNextToken(Lexer::T_OR)) {
 $this->match(Lexer::T_OR);
 $conditionalTerms[] = $this->ConditionalTerm();
 }
 // Phase 1 AST optimization: Prevent AST\ConditionalExpression
 // if only one AST\ConditionalTerm is defined
 if (count($conditionalTerms) === 1) {
 return $conditionalTerms[0];
 }
 return new AST\ConditionalExpression($conditionalTerms);
 }
 public function ConditionalTerm()
 {
 $conditionalFactors = [];
 $conditionalFactors[] = $this->ConditionalFactor();
 while ($this->lexer->isNextToken(Lexer::T_AND)) {
 $this->match(Lexer::T_AND);
 $conditionalFactors[] = $this->ConditionalFactor();
 }
 // Phase 1 AST optimization: Prevent AST\ConditionalTerm
 // if only one AST\ConditionalFactor is defined
 if (count($conditionalFactors) === 1) {
 return $conditionalFactors[0];
 }
 return new AST\ConditionalTerm($conditionalFactors);
 }
 public function ConditionalFactor()
 {
 $not = \false;
 if ($this->lexer->isNextToken(Lexer::T_NOT)) {
 $this->match(Lexer::T_NOT);
 $not = \true;
 }
 $conditionalPrimary = $this->ConditionalPrimary();
 // Phase 1 AST optimization: Prevent AST\ConditionalFactor
 // if only one AST\ConditionalPrimary is defined
 if (!$not) {
 return $conditionalPrimary;
 }
 return new AST\ConditionalFactor($conditionalPrimary, $not);
 }
 public function ConditionalPrimary()
 {
 $condPrimary = new AST\ConditionalPrimary();
 if (!$this->lexer->isNextToken(Lexer::T_OPEN_PARENTHESIS)) {
 $condPrimary->simpleConditionalExpression = $this->SimpleConditionalExpression();
 return $condPrimary;
 }
 // Peek beyond the matching closing parenthesis ')'
 $peek = $this->peekBeyondClosingParenthesis();
 if ($peek !== null && (in_array($peek['value'], ['=', '<', '<=', '<>', '>', '>=', '!='], \true) || in_array($peek['type'], [Lexer::T_NOT, Lexer::T_BETWEEN, Lexer::T_LIKE, Lexer::T_IN, Lexer::T_IS, Lexer::T_EXISTS], \true) || $this->isMathOperator($peek))) {
 $condPrimary->simpleConditionalExpression = $this->SimpleConditionalExpression();
 return $condPrimary;
 }
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $condPrimary->conditionalExpression = $this->ConditionalExpression();
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return $condPrimary;
 }
 public function SimpleConditionalExpression()
 {
 assert($this->lexer->lookahead !== null);
 if ($this->lexer->isNextToken(Lexer::T_EXISTS)) {
 return $this->ExistsExpression();
 }
 $token = $this->lexer->lookahead;
 $peek = $this->lexer->glimpse();
 $lookahead = $token;
 if ($this->lexer->isNextToken(Lexer::T_NOT)) {
 $token = $this->lexer->glimpse();
 }
 assert($token !== null);
 assert($peek !== null);
 if ($token['type'] === Lexer::T_IDENTIFIER || $token['type'] === Lexer::T_INPUT_PARAMETER || $this->isFunction()) {
 // Peek beyond the matching closing parenthesis.
 $beyond = $this->lexer->peek();
 switch ($peek['value']) {
 case '(':
 // Peeks beyond the matched closing parenthesis.
 $token = $this->peekBeyondClosingParenthesis(\false);
 assert($token !== null);
 if ($token['type'] === Lexer::T_NOT) {
 $token = $this->lexer->peek();
 assert($token !== null);
 }
 if ($token['type'] === Lexer::T_IS) {
 $lookahead = $this->lexer->peek();
 }
 break;
 default:
 // Peek beyond the PathExpression or InputParameter.
 $token = $beyond;
 while ($token['value'] === '.') {
 $this->lexer->peek();
 $token = $this->lexer->peek();
 assert($token !== null);
 }
 // Also peek beyond a NOT if there is one.
 assert($token !== null);
 if ($token['type'] === Lexer::T_NOT) {
 $token = $this->lexer->peek();
 assert($token !== null);
 }
 // We need to go even further in case of IS (differentiate between NULL and EMPTY)
 $lookahead = $this->lexer->peek();
 }
 assert($lookahead !== null);
 // Also peek beyond a NOT if there is one.
 if ($lookahead['type'] === Lexer::T_NOT) {
 $lookahead = $this->lexer->peek();
 }
 $this->lexer->resetPeek();
 }
 if ($token['type'] === Lexer::T_BETWEEN) {
 return $this->BetweenExpression();
 }
 if ($token['type'] === Lexer::T_LIKE) {
 return $this->LikeExpression();
 }
 if ($token['type'] === Lexer::T_IN) {
 return $this->InExpression();
 }
 if ($token['type'] === Lexer::T_INSTANCE) {
 return $this->InstanceOfExpression();
 }
 if ($token['type'] === Lexer::T_MEMBER) {
 return $this->CollectionMemberExpression();
 }
 assert($lookahead !== null);
 if ($token['type'] === Lexer::T_IS && $lookahead['type'] === Lexer::T_NULL) {
 return $this->NullComparisonExpression();
 }
 if ($token['type'] === Lexer::T_IS && $lookahead['type'] === Lexer::T_EMPTY) {
 return $this->EmptyCollectionComparisonExpression();
 }
 return $this->ComparisonExpression();
 }
 public function EmptyCollectionComparisonExpression()
 {
 $pathExpression = $this->CollectionValuedPathExpression();
 $this->match(Lexer::T_IS);
 $not = \false;
 if ($this->lexer->isNextToken(Lexer::T_NOT)) {
 $this->match(Lexer::T_NOT);
 $not = \true;
 }
 $this->match(Lexer::T_EMPTY);
 return new AST\EmptyCollectionComparisonExpression($pathExpression, $not);
 }
 public function CollectionMemberExpression()
 {
 $not = \false;
 $entityExpr = $this->EntityExpression();
 if ($this->lexer->isNextToken(Lexer::T_NOT)) {
 $this->match(Lexer::T_NOT);
 $not = \true;
 }
 $this->match(Lexer::T_MEMBER);
 if ($this->lexer->isNextToken(Lexer::T_OF)) {
 $this->match(Lexer::T_OF);
 }
 return new AST\CollectionMemberExpression($entityExpr, $this->CollectionValuedPathExpression(), $not);
 }
 public function Literal()
 {
 assert($this->lexer->lookahead !== null);
 assert($this->lexer->token !== null);
 switch ($this->lexer->lookahead['type']) {
 case Lexer::T_STRING:
 $this->match(Lexer::T_STRING);
 return new AST\Literal(AST\Literal::STRING, $this->lexer->token['value']);
 case Lexer::T_INTEGER:
 case Lexer::T_FLOAT:
 $this->match($this->lexer->isNextToken(Lexer::T_INTEGER) ? Lexer::T_INTEGER : Lexer::T_FLOAT);
 return new AST\Literal(AST\Literal::NUMERIC, $this->lexer->token['value']);
 case Lexer::T_TRUE:
 case Lexer::T_FALSE:
 $this->match($this->lexer->isNextToken(Lexer::T_TRUE) ? Lexer::T_TRUE : Lexer::T_FALSE);
 return new AST\Literal(AST\Literal::BOOLEAN, $this->lexer->token['value']);
 default:
 $this->syntaxError('Literal');
 }
 }
 public function InParameter()
 {
 assert($this->lexer->lookahead !== null);
 if ($this->lexer->lookahead['type'] === Lexer::T_INPUT_PARAMETER) {
 return $this->InputParameter();
 }
 return $this->ArithmeticExpression();
 }
 public function InputParameter()
 {
 $this->match(Lexer::T_INPUT_PARAMETER);
 assert($this->lexer->token !== null);
 return new AST\InputParameter($this->lexer->token['value']);
 }
 public function ArithmeticExpression()
 {
 $expr = new AST\ArithmeticExpression();
 if ($this->lexer->isNextToken(Lexer::T_OPEN_PARENTHESIS)) {
 $peek = $this->lexer->glimpse();
 assert($peek !== null);
 if ($peek['type'] === Lexer::T_SELECT) {
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $expr->subselect = $this->Subselect();
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return $expr;
 }
 }
 $expr->simpleArithmeticExpression = $this->SimpleArithmeticExpression();
 return $expr;
 }
 public function SimpleArithmeticExpression()
 {
 $terms = [];
 $terms[] = $this->ArithmeticTerm();
 while (($isPlus = $this->lexer->isNextToken(Lexer::T_PLUS)) || $this->lexer->isNextToken(Lexer::T_MINUS)) {
 $this->match($isPlus ? Lexer::T_PLUS : Lexer::T_MINUS);
 assert($this->lexer->token !== null);
 $terms[] = $this->lexer->token['value'];
 $terms[] = $this->ArithmeticTerm();
 }
 // Phase 1 AST optimization: Prevent AST\SimpleArithmeticExpression
 // if only one AST\ArithmeticTerm is defined
 if (count($terms) === 1) {
 return $terms[0];
 }
 return new AST\SimpleArithmeticExpression($terms);
 }
 public function ArithmeticTerm()
 {
 $factors = [];
 $factors[] = $this->ArithmeticFactor();
 while (($isMult = $this->lexer->isNextToken(Lexer::T_MULTIPLY)) || $this->lexer->isNextToken(Lexer::T_DIVIDE)) {
 $this->match($isMult ? Lexer::T_MULTIPLY : Lexer::T_DIVIDE);
 assert($this->lexer->token !== null);
 $factors[] = $this->lexer->token['value'];
 $factors[] = $this->ArithmeticFactor();
 }
 // Phase 1 AST optimization: Prevent AST\ArithmeticTerm
 // if only one AST\ArithmeticFactor is defined
 if (count($factors) === 1) {
 return $factors[0];
 }
 return new AST\ArithmeticTerm($factors);
 }
 public function ArithmeticFactor()
 {
 $sign = null;
 $isPlus = $this->lexer->isNextToken(Lexer::T_PLUS);
 if ($isPlus || $this->lexer->isNextToken(Lexer::T_MINUS)) {
 $this->match($isPlus ? Lexer::T_PLUS : Lexer::T_MINUS);
 $sign = $isPlus;
 }
 $primary = $this->ArithmeticPrimary();
 // Phase 1 AST optimization: Prevent AST\ArithmeticFactor
 // if only one AST\ArithmeticPrimary is defined
 if ($sign === null) {
 return $primary;
 }
 return new AST\ArithmeticFactor($primary, $sign);
 }
 public function ArithmeticPrimary()
 {
 if ($this->lexer->isNextToken(Lexer::T_OPEN_PARENTHESIS)) {
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $expr = $this->SimpleArithmeticExpression();
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return new AST\ParenthesisExpression($expr);
 }
 assert($this->lexer->lookahead !== null);
 switch ($this->lexer->lookahead['type']) {
 case Lexer::T_COALESCE:
 case Lexer::T_NULLIF:
 case Lexer::T_CASE:
 return $this->CaseExpression();
 case Lexer::T_IDENTIFIER:
 $peek = $this->lexer->glimpse();
 if ($peek !== null && $peek['value'] === '(') {
 return $this->FunctionDeclaration();
 }
 if ($peek !== null && $peek['value'] === '.') {
 return $this->SingleValuedPathExpression();
 }
 if (isset($this->queryComponents[$this->lexer->lookahead['value']]['resultVariable'])) {
 return $this->ResultVariable();
 }
 return $this->StateFieldPathExpression();
 case Lexer::T_INPUT_PARAMETER:
 return $this->InputParameter();
 default:
 $peek = $this->lexer->glimpse();
 if ($peek !== null && $peek['value'] === '(') {
 return $this->FunctionDeclaration();
 }
 return $this->Literal();
 }
 }
 public function StringExpression()
 {
 $peek = $this->lexer->glimpse();
 assert($peek !== null);
 // Subselect
 if ($this->lexer->isNextToken(Lexer::T_OPEN_PARENTHESIS) && $peek['type'] === Lexer::T_SELECT) {
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $expr = $this->Subselect();
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return $expr;
 }
 assert($this->lexer->lookahead !== null);
 // ResultVariable (string)
 if ($this->lexer->isNextToken(Lexer::T_IDENTIFIER) && isset($this->queryComponents[$this->lexer->lookahead['value']]['resultVariable'])) {
 return $this->ResultVariable();
 }
 return $this->StringPrimary();
 }
 public function StringPrimary()
 {
 assert($this->lexer->lookahead !== null);
 $lookaheadType = $this->lexer->lookahead['type'];
 switch ($lookaheadType) {
 case Lexer::T_IDENTIFIER:
 $peek = $this->lexer->glimpse();
 assert($peek !== null);
 if ($peek['value'] === '.') {
 return $this->StateFieldPathExpression();
 }
 if ($peek['value'] === '(') {
 // do NOT directly go to FunctionsReturningString() because it doesn't check for custom functions.
 return $this->FunctionDeclaration();
 }
 $this->syntaxError("'.' or '('");
 break;
 case Lexer::T_STRING:
 $this->match(Lexer::T_STRING);
 assert($this->lexer->token !== null);
 return new AST\Literal(AST\Literal::STRING, $this->lexer->token['value']);
 case Lexer::T_INPUT_PARAMETER:
 return $this->InputParameter();
 case Lexer::T_CASE:
 case Lexer::T_COALESCE:
 case Lexer::T_NULLIF:
 return $this->CaseExpression();
 default:
 assert($lookaheadType !== null);
 if ($this->isAggregateFunction($lookaheadType)) {
 return $this->AggregateExpression();
 }
 }
 $this->syntaxError('StateFieldPathExpression | string | InputParameter | FunctionsReturningStrings | AggregateExpression');
 }
 public function EntityExpression()
 {
 $glimpse = $this->lexer->glimpse();
 assert($glimpse !== null);
 if ($this->lexer->isNextToken(Lexer::T_IDENTIFIER) && $glimpse['value'] === '.') {
 return $this->SingleValuedAssociationPathExpression();
 }
 return $this->SimpleEntityExpression();
 }
 public function SimpleEntityExpression()
 {
 if ($this->lexer->isNextToken(Lexer::T_INPUT_PARAMETER)) {
 return $this->InputParameter();
 }
 return $this->StateFieldPathExpression();
 }
 public function AggregateExpression()
 {
 assert($this->lexer->lookahead !== null);
 $lookaheadType = $this->lexer->lookahead['type'];
 $isDistinct = \false;
 if (!in_array($lookaheadType, [Lexer::T_COUNT, Lexer::T_AVG, Lexer::T_MAX, Lexer::T_MIN, Lexer::T_SUM], \true)) {
 $this->syntaxError('One of: MAX, MIN, AVG, SUM, COUNT');
 }
 $this->match($lookaheadType);
 assert($this->lexer->token !== null);
 $functionName = $this->lexer->token['value'];
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 if ($this->lexer->isNextToken(Lexer::T_DISTINCT)) {
 $this->match(Lexer::T_DISTINCT);
 $isDistinct = \true;
 }
 $pathExp = $this->SimpleArithmeticExpression();
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return new AST\AggregateExpression($functionName, $pathExp, $isDistinct);
 }
 public function QuantifiedExpression()
 {
 assert($this->lexer->lookahead !== null);
 $lookaheadType = $this->lexer->lookahead['type'];
 $value = $this->lexer->lookahead['value'];
 if (!in_array($lookaheadType, [Lexer::T_ALL, Lexer::T_ANY, Lexer::T_SOME], \true)) {
 $this->syntaxError('ALL, ANY or SOME');
 }
 $this->match($lookaheadType);
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $qExpr = new AST\QuantifiedExpression($this->Subselect());
 $qExpr->type = $value;
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return $qExpr;
 }
 public function BetweenExpression()
 {
 $not = \false;
 $arithExpr1 = $this->ArithmeticExpression();
 if ($this->lexer->isNextToken(Lexer::T_NOT)) {
 $this->match(Lexer::T_NOT);
 $not = \true;
 }
 $this->match(Lexer::T_BETWEEN);
 $arithExpr2 = $this->ArithmeticExpression();
 $this->match(Lexer::T_AND);
 $arithExpr3 = $this->ArithmeticExpression();
 return new AST\BetweenExpression($arithExpr1, $arithExpr2, $arithExpr3, $not);
 }
 public function ComparisonExpression()
 {
 $this->lexer->glimpse();
 $leftExpr = $this->ArithmeticExpression();
 $operator = $this->ComparisonOperator();
 $rightExpr = $this->isNextAllAnySome() ? $this->QuantifiedExpression() : $this->ArithmeticExpression();
 return new AST\ComparisonExpression($leftExpr, $operator, $rightExpr);
 }
 public function InExpression()
 {
 $expression = $this->ArithmeticExpression();
 $not = \false;
 if ($this->lexer->isNextToken(Lexer::T_NOT)) {
 $this->match(Lexer::T_NOT);
 $not = \true;
 }
 $this->match(Lexer::T_IN);
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 if ($this->lexer->isNextToken(Lexer::T_SELECT)) {
 $inExpression = new AST\InSubselectExpression($expression, $this->Subselect(), $not);
 } else {
 $literals = [$this->InParameter()];
 while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
 $this->match(Lexer::T_COMMA);
 $literals[] = $this->InParameter();
 }
 $inExpression = new AST\InListExpression($expression, $literals, $not);
 }
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return $inExpression;
 }
 public function InstanceOfExpression()
 {
 $identificationVariable = $this->IdentificationVariable();
 $not = \false;
 if ($this->lexer->isNextToken(Lexer::T_NOT)) {
 $this->match(Lexer::T_NOT);
 $not = \true;
 }
 $this->match(Lexer::T_INSTANCE);
 $this->match(Lexer::T_OF);
 $exprValues = $this->lexer->isNextToken(Lexer::T_OPEN_PARENTHESIS) ? $this->InstanceOfParameterList() : [$this->InstanceOfParameter()];
 return new AST\InstanceOfExpression($identificationVariable, $exprValues, $not);
 }
 public function InstanceOfParameterList() : array
 {
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $exprValues = [$this->InstanceOfParameter()];
 while ($this->lexer->isNextToken(Lexer::T_COMMA)) {
 $this->match(Lexer::T_COMMA);
 $exprValues[] = $this->InstanceOfParameter();
 }
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return $exprValues;
 }
 public function InstanceOfParameter()
 {
 if ($this->lexer->isNextToken(Lexer::T_INPUT_PARAMETER)) {
 $this->match(Lexer::T_INPUT_PARAMETER);
 assert($this->lexer->token !== null);
 return new AST\InputParameter($this->lexer->token['value']);
 }
 $abstractSchemaName = $this->AbstractSchemaName();
 $this->validateAbstractSchemaName($abstractSchemaName);
 return $abstractSchemaName;
 }
 public function LikeExpression()
 {
 $stringExpr = $this->StringExpression();
 $not = \false;
 if ($this->lexer->isNextToken(Lexer::T_NOT)) {
 $this->match(Lexer::T_NOT);
 $not = \true;
 }
 $this->match(Lexer::T_LIKE);
 if ($this->lexer->isNextToken(Lexer::T_INPUT_PARAMETER)) {
 $this->match(Lexer::T_INPUT_PARAMETER);
 assert($this->lexer->token !== null);
 $stringPattern = new AST\InputParameter($this->lexer->token['value']);
 } else {
 $stringPattern = $this->StringPrimary();
 }
 $escapeChar = null;
 if ($this->lexer->lookahead !== null && $this->lexer->lookahead['type'] === Lexer::T_ESCAPE) {
 $this->match(Lexer::T_ESCAPE);
 $this->match(Lexer::T_STRING);
 assert($this->lexer->token !== null);
 $escapeChar = new AST\Literal(AST\Literal::STRING, $this->lexer->token['value']);
 }
 return new AST\LikeExpression($stringExpr, $stringPattern, $escapeChar, $not);
 }
 public function NullComparisonExpression()
 {
 switch (\true) {
 case $this->lexer->isNextToken(Lexer::T_INPUT_PARAMETER):
 $this->match(Lexer::T_INPUT_PARAMETER);
 assert($this->lexer->token !== null);
 $expr = new AST\InputParameter($this->lexer->token['value']);
 break;
 case $this->lexer->isNextToken(Lexer::T_NULLIF):
 $expr = $this->NullIfExpression();
 break;
 case $this->lexer->isNextToken(Lexer::T_COALESCE):
 $expr = $this->CoalesceExpression();
 break;
 case $this->isFunction():
 $expr = $this->FunctionDeclaration();
 break;
 default:
 // We need to check if we are in a IdentificationVariable or SingleValuedPathExpression
 $glimpse = $this->lexer->glimpse();
 assert($glimpse !== null);
 if ($glimpse['type'] === Lexer::T_DOT) {
 $expr = $this->SingleValuedPathExpression();
 // Leave switch statement
 break;
 }
 assert($this->lexer->lookahead !== null);
 $lookaheadValue = $this->lexer->lookahead['value'];
 // Validate existing component
 if (!isset($this->queryComponents[$lookaheadValue])) {
 $this->semanticalError('Cannot add having condition on undefined result variable.');
 }
 // Validate SingleValuedPathExpression (ie.: "product")
 if (isset($this->queryComponents[$lookaheadValue]['metadata'])) {
 $expr = $this->SingleValuedPathExpression();
 break;
 }
 // Validating ResultVariable
 if (!isset($this->queryComponents[$lookaheadValue]['resultVariable'])) {
 $this->semanticalError('Cannot add having condition on a non result variable.');
 }
 $expr = $this->ResultVariable();
 break;
 }
 $this->match(Lexer::T_IS);
 $not = \false;
 if ($this->lexer->isNextToken(Lexer::T_NOT)) {
 $this->match(Lexer::T_NOT);
 $not = \true;
 }
 $this->match(Lexer::T_NULL);
 return new AST\NullComparisonExpression($expr, $not);
 }
 public function ExistsExpression()
 {
 $not = \false;
 if ($this->lexer->isNextToken(Lexer::T_NOT)) {
 $this->match(Lexer::T_NOT);
 $not = \true;
 }
 $this->match(Lexer::T_EXISTS);
 $this->match(Lexer::T_OPEN_PARENTHESIS);
 $subselect = $this->Subselect();
 $this->match(Lexer::T_CLOSE_PARENTHESIS);
 return new AST\ExistsExpression($subselect, $not);
 }
 public function ComparisonOperator()
 {
 assert($this->lexer->lookahead !== null);
 switch ($this->lexer->lookahead['value']) {
 case '=':
 $this->match(Lexer::T_EQUALS);
 return '=';
 case '<':
 $this->match(Lexer::T_LOWER_THAN);
 $operator = '<';
 if ($this->lexer->isNextToken(Lexer::T_EQUALS)) {
 $this->match(Lexer::T_EQUALS);
 $operator .= '=';
 } elseif ($this->lexer->isNextToken(Lexer::T_GREATER_THAN)) {
 $this->match(Lexer::T_GREATER_THAN);
 $operator .= '>';
 }
 return $operator;
 case '>':
 $this->match(Lexer::T_GREATER_THAN);
 $operator = '>';
 if ($this->lexer->isNextToken(Lexer::T_EQUALS)) {
 $this->match(Lexer::T_EQUALS);
 $operator .= '=';
 }
 return $operator;
 case '!':
 $this->match(Lexer::T_NEGATE);
 $this->match(Lexer::T_EQUALS);
 return '<>';
 default:
 $this->syntaxError('=, <, <=, <>, >, >=, !=');
 }
 }
 public function FunctionDeclaration()
 {
 assert($this->lexer->lookahead !== null);
 $token = $this->lexer->lookahead;
 $funcName = strtolower($token['value']);
 $customFunctionDeclaration = $this->CustomFunctionDeclaration();
 // Check for custom functions functions first!
 switch (\true) {
 case $customFunctionDeclaration !== null:
 return $customFunctionDeclaration;
 case isset(self::$stringFunctions[$funcName]):
 return $this->FunctionsReturningStrings();
 case isset(self::$numericFunctions[$funcName]):
 return $this->FunctionsReturningNumerics();
 case isset(self::$datetimeFunctions[$funcName]):
 return $this->FunctionsReturningDatetime();
 default:
 $this->syntaxError('known function', $token);
 }
 }
 private function CustomFunctionDeclaration() : ?Functions\FunctionNode
 {
 assert($this->lexer->lookahead !== null);
 $token = $this->lexer->lookahead;
 $funcName = strtolower($token['value']);
 // Check for custom functions afterwards
 $config = $this->em->getConfiguration();
 switch (\true) {
 case $config->getCustomStringFunction($funcName) !== null:
 return $this->CustomFunctionsReturningStrings();
 case $config->getCustomNumericFunction($funcName) !== null:
 return $this->CustomFunctionsReturningNumerics();
 case $config->getCustomDatetimeFunction($funcName) !== null:
 return $this->CustomFunctionsReturningDatetime();
 default:
 return null;
 }
 }
 public function FunctionsReturningNumerics()
 {
 assert($this->lexer->lookahead !== null);
 $funcNameLower = strtolower($this->lexer->lookahead['value']);
 $funcClass = self::$numericFunctions[$funcNameLower];
 $function = new $funcClass($funcNameLower);
 $function->parse($this);
 return $function;
 }
 public function CustomFunctionsReturningNumerics()
 {
 assert($this->lexer->lookahead !== null);
 // getCustomNumericFunction is case-insensitive
 $functionName = strtolower($this->lexer->lookahead['value']);
 $functionClass = $this->em->getConfiguration()->getCustomNumericFunction($functionName);
 assert($functionClass !== null);
 $function = is_string($functionClass) ? new $functionClass($functionName) : $functionClass($functionName);
 $function->parse($this);
 return $function;
 }
 public function FunctionsReturningDatetime()
 {
 assert($this->lexer->lookahead !== null);
 $funcNameLower = strtolower($this->lexer->lookahead['value']);
 $funcClass = self::$datetimeFunctions[$funcNameLower];
 $function = new $funcClass($funcNameLower);
 $function->parse($this);
 return $function;
 }
 public function CustomFunctionsReturningDatetime()
 {
 assert($this->lexer->lookahead !== null);
 // getCustomDatetimeFunction is case-insensitive
 $functionName = $this->lexer->lookahead['value'];
 $functionClass = $this->em->getConfiguration()->getCustomDatetimeFunction($functionName);
 assert($functionClass !== null);
 $function = is_string($functionClass) ? new $functionClass($functionName) : $functionClass($functionName);
 $function->parse($this);
 return $function;
 }
 public function FunctionsReturningStrings()
 {
 assert($this->lexer->lookahead !== null);
 $funcNameLower = strtolower($this->lexer->lookahead['value']);
 $funcClass = self::$stringFunctions[$funcNameLower];
 $function = new $funcClass($funcNameLower);
 $function->parse($this);
 return $function;
 }
 public function CustomFunctionsReturningStrings()
 {
 assert($this->lexer->lookahead !== null);
 // getCustomStringFunction is case-insensitive
 $functionName = $this->lexer->lookahead['value'];
 $functionClass = $this->em->getConfiguration()->getCustomStringFunction($functionName);
 assert($functionClass !== null);
 $function = is_string($functionClass) ? new $functionClass($functionName) : $functionClass($functionName);
 $function->parse($this);
 return $function;
 }
 private function getMetadataForDqlAlias(string $dqlAlias) : ClassMetadata
 {
 if (!isset($this->queryComponents[$dqlAlias]['metadata'])) {
 throw new LogicException(sprintf('No metadata for DQL alias: %s', $dqlAlias));
 }
 return $this->queryComponents[$dqlAlias]['metadata'];
 }
}
