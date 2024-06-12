<?php
namespace MailPoetVendor\Sabberworm\CSS\RuleSet;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\Comment\Comment;
use MailPoetVendor\Sabberworm\CSS\Comment\Commentable;
use MailPoetVendor\Sabberworm\CSS\OutputFormat;
use MailPoetVendor\Sabberworm\CSS\Parsing\ParserState;
use MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedEOFException;
use MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedTokenException;
use MailPoetVendor\Sabberworm\CSS\Renderable;
use MailPoetVendor\Sabberworm\CSS\Rule\Rule;
abstract class RuleSet implements Renderable, Commentable
{
 private $aRules;
 protected $iLineNo;
 protected $aComments;
 public function __construct($iLineNo = 0)
 {
 $this->aRules = [];
 $this->iLineNo = $iLineNo;
 $this->aComments = [];
 }
 public static function parseRuleSet(ParserState $oParserState, RuleSet $oRuleSet)
 {
 while ($oParserState->comes(';')) {
 $oParserState->consume(';');
 }
 while (!$oParserState->comes('}')) {
 $oRule = null;
 if ($oParserState->getSettings()->bLenientParsing) {
 try {
 $oRule = Rule::parse($oParserState);
 } catch (UnexpectedTokenException $e) {
 try {
 $sConsume = $oParserState->consumeUntil(["\n", ";", '}'], \true);
 // We need to “unfind” the matches to the end of the ruleSet as this will be matched later
 if ($oParserState->streql(\substr($sConsume, -1), '}')) {
 $oParserState->backtrack(1);
 } else {
 while ($oParserState->comes(';')) {
 $oParserState->consume(';');
 }
 }
 } catch (UnexpectedTokenException $e) {
 // We’ve reached the end of the document. Just close the RuleSet.
 return;
 }
 }
 } else {
 $oRule = Rule::parse($oParserState);
 }
 if ($oRule) {
 $oRuleSet->addRule($oRule);
 }
 }
 $oParserState->consume('}');
 }
 public function getLineNo()
 {
 return $this->iLineNo;
 }
 public function addRule(Rule $oRule, Rule $oSibling = null)
 {
 $sRule = $oRule->getRule();
 if (!isset($this->aRules[$sRule])) {
 $this->aRules[$sRule] = [];
 }
 $iPosition = \count($this->aRules[$sRule]);
 if ($oSibling !== null) {
 $iSiblingPos = \array_search($oSibling, $this->aRules[$sRule], \true);
 if ($iSiblingPos !== \false) {
 $iPosition = $iSiblingPos;
 $oRule->setPosition($oSibling->getLineNo(), $oSibling->getColNo() - 1);
 }
 }
 if ($oRule->getLineNo() === 0 && $oRule->getColNo() === 0) {
 //this node is added manually, give it the next best line
 $rules = $this->getRules();
 $pos = \count($rules);
 if ($pos > 0) {
 $last = $rules[$pos - 1];
 $oRule->setPosition($last->getLineNo() + 1, 0);
 }
 }
 \array_splice($this->aRules[$sRule], $iPosition, 0, [$oRule]);
 }
 public function getRules($mRule = null)
 {
 if ($mRule instanceof Rule) {
 $mRule = $mRule->getRule();
 }
 $aResult = [];
 foreach ($this->aRules as $sName => $aRules) {
 // Either no search rule is given or the search rule matches the found rule exactly
 // or the search rule ends in “-” and the found rule starts with the search rule.
 if (!$mRule || $sName === $mRule || \strrpos($mRule, '-') === \strlen($mRule) - \strlen('-') && (\strpos($sName, $mRule) === 0 || $sName === \substr($mRule, 0, -1))) {
 $aResult = \array_merge($aResult, $aRules);
 }
 }
 \usort($aResult, function (Rule $first, Rule $second) {
 if ($first->getLineNo() === $second->getLineNo()) {
 return $first->getColNo() - $second->getColNo();
 }
 return $first->getLineNo() - $second->getLineNo();
 });
 return $aResult;
 }
 public function setRules(array $aRules)
 {
 $this->aRules = [];
 foreach ($aRules as $rule) {
 $this->addRule($rule);
 }
 }
 public function getRulesAssoc($mRule = null)
 {
 $aResult = [];
 foreach ($this->getRules($mRule) as $oRule) {
 $aResult[$oRule->getRule()] = $oRule;
 }
 return $aResult;
 }
 public function removeRule($mRule)
 {
 if ($mRule instanceof Rule) {
 $sRule = $mRule->getRule();
 if (!isset($this->aRules[$sRule])) {
 return;
 }
 foreach ($this->aRules[$sRule] as $iKey => $oRule) {
 if ($oRule === $mRule) {
 unset($this->aRules[$sRule][$iKey]);
 }
 }
 } else {
 foreach ($this->aRules as $sName => $aRules) {
 // Either no search rule is given or the search rule matches the found rule exactly
 // or the search rule ends in “-” and the found rule starts with the search rule or equals it
 // (without the trailing dash).
 if (!$mRule || $sName === $mRule || \strrpos($mRule, '-') === \strlen($mRule) - \strlen('-') && (\strpos($sName, $mRule) === 0 || $sName === \substr($mRule, 0, -1))) {
 unset($this->aRules[$sName]);
 }
 }
 }
 }
 public function __toString()
 {
 return $this->render(new OutputFormat());
 }
 public function render(OutputFormat $oOutputFormat)
 {
 $sResult = '';
 $bIsFirst = \true;
 foreach ($this->aRules as $aRules) {
 foreach ($aRules as $oRule) {
 $sRendered = $oOutputFormat->safely(function () use($oRule, $oOutputFormat) {
 return $oRule->render($oOutputFormat->nextLevel());
 });
 if ($sRendered === null) {
 continue;
 }
 if ($bIsFirst) {
 $bIsFirst = \false;
 $sResult .= $oOutputFormat->nextLevel()->spaceBeforeRules();
 } else {
 $sResult .= $oOutputFormat->nextLevel()->spaceBetweenRules();
 }
 $sResult .= $sRendered;
 }
 }
 if (!$bIsFirst) {
 // Had some output
 $sResult .= $oOutputFormat->spaceAfterRules();
 }
 return $oOutputFormat->removeLastSemicolon($sResult);
 }
 public function addComments(array $aComments)
 {
 $this->aComments = \array_merge($this->aComments, $aComments);
 }
 public function getComments()
 {
 return $this->aComments;
 }
 public function setComments(array $aComments)
 {
 $this->aComments = $aComments;
 }
}
