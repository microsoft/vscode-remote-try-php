<?php
namespace MailPoetVendor\Sabberworm\CSS;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\Comment\Commentable;
use MailPoetVendor\Sabberworm\CSS\Parsing\OutputException;
class OutputFormatter
{
 private $oFormat;
 public function __construct(OutputFormat $oFormat)
 {
 $this->oFormat = $oFormat;
 }
 public function space($sName, $sType = null)
 {
 $sSpaceString = $this->oFormat->get("Space{$sName}");
 // If $sSpaceString is an array, we have multiple values configured
 // depending on the type of object the space applies to
 if (\is_array($sSpaceString)) {
 if ($sType !== null && isset($sSpaceString[$sType])) {
 $sSpaceString = $sSpaceString[$sType];
 } else {
 $sSpaceString = \reset($sSpaceString);
 }
 }
 return $this->prepareSpace($sSpaceString);
 }
 public function spaceAfterRuleName()
 {
 return $this->space('AfterRuleName');
 }
 public function spaceBeforeRules()
 {
 return $this->space('BeforeRules');
 }
 public function spaceAfterRules()
 {
 return $this->space('AfterRules');
 }
 public function spaceBetweenRules()
 {
 return $this->space('BetweenRules');
 }
 public function spaceBeforeBlocks()
 {
 return $this->space('BeforeBlocks');
 }
 public function spaceAfterBlocks()
 {
 return $this->space('AfterBlocks');
 }
 public function spaceBetweenBlocks()
 {
 return $this->space('BetweenBlocks');
 }
 public function spaceBeforeSelectorSeparator()
 {
 return $this->space('BeforeSelectorSeparator');
 }
 public function spaceAfterSelectorSeparator()
 {
 return $this->space('AfterSelectorSeparator');
 }
 public function spaceBeforeListArgumentSeparator($sSeparator)
 {
 return $this->space('BeforeListArgumentSeparator', $sSeparator);
 }
 public function spaceAfterListArgumentSeparator($sSeparator)
 {
 return $this->space('AfterListArgumentSeparator', $sSeparator);
 }
 public function spaceBeforeOpeningBrace()
 {
 return $this->space('BeforeOpeningBrace');
 }
 public function safely($cCode)
 {
 if ($this->oFormat->get('IgnoreExceptions')) {
 // If output exceptions are ignored, run the code with exception guards
 try {
 return $cCode();
 } catch (OutputException $e) {
 return null;
 }
 // Do nothing
 } else {
 // Run the code as-is
 return $cCode();
 }
 }
 public function implode($sSeparator, array $aValues, $bIncreaseLevel = \false)
 {
 $sResult = '';
 $oFormat = $this->oFormat;
 if ($bIncreaseLevel) {
 $oFormat = $oFormat->nextLevel();
 }
 $bIsFirst = \true;
 foreach ($aValues as $mValue) {
 if ($bIsFirst) {
 $bIsFirst = \false;
 } else {
 $sResult .= $sSeparator;
 }
 if ($mValue instanceof Renderable) {
 $sResult .= $mValue->render($oFormat);
 } else {
 $sResult .= $mValue;
 }
 }
 return $sResult;
 }
 public function removeLastSemicolon($sString)
 {
 if ($this->oFormat->get('SemicolonAfterLastRule')) {
 return $sString;
 }
 $sString = \explode(';', $sString);
 if (\count($sString) < 2) {
 return $sString[0];
 }
 $sLast = \array_pop($sString);
 $sNextToLast = \array_pop($sString);
 \array_push($sString, $sNextToLast . $sLast);
 return \implode(';', $sString);
 }
 public function comments(Commentable $oCommentable)
 {
 if (!$this->oFormat->bRenderComments) {
 return '';
 }
 $sResult = '';
 $aComments = $oCommentable->getComments();
 $iLastCommentIndex = \count($aComments) - 1;
 foreach ($aComments as $i => $oComment) {
 $sResult .= $oComment->render($this->oFormat);
 $sResult .= $i === $iLastCommentIndex ? $this->spaceAfterBlocks() : $this->spaceBetweenBlocks();
 }
 return $sResult;
 }
 private function prepareSpace($sSpaceString)
 {
 return \str_replace("\n", "\n" . $this->indent(), $sSpaceString);
 }
 private function indent()
 {
 return \str_repeat($this->oFormat->sIndentation, $this->oFormat->level());
 }
}
