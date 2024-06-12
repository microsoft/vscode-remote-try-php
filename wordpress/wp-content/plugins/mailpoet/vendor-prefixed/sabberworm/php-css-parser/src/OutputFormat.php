<?php
namespace MailPoetVendor\Sabberworm\CSS;
if (!defined('ABSPATH')) exit;
class OutputFormat
{
 public $sStringQuotingType = '"';
 public $bRGBHashNotation = \true;
 public $bSemicolonAfterLastRule = \true;
 public $sSpaceAfterRuleName = ' ';
 public $sSpaceBeforeRules = '';
 public $sSpaceAfterRules = '';
 public $sSpaceBetweenRules = '';
 public $sSpaceBeforeBlocks = '';
 public $sSpaceAfterBlocks = '';
 public $sSpaceBetweenBlocks = "\n";
 public $sBeforeAtRuleBlock = '';
 public $sAfterAtRuleBlock = '';
 public $sSpaceBeforeSelectorSeparator = '';
 public $sSpaceAfterSelectorSeparator = ' ';
 public $sSpaceBeforeListArgumentSeparator = '';
 public $sSpaceAfterListArgumentSeparator = '';
 public $sSpaceBeforeOpeningBrace = ' ';
 public $sBeforeDeclarationBlock = '';
 public $sAfterDeclarationBlockSelectors = '';
 public $sAfterDeclarationBlock = '';
 public $sIndentation = "\t";
 public $bIgnoreExceptions = \false;
 private $oFormatter = null;
 private $oNextLevelFormat = null;
 private $iIndentationLevel = 0;
 public function __construct()
 {
 }
 public function get($sName)
 {
 $aVarPrefixes = ['a', 's', 'm', 'b', 'f', 'o', 'c', 'i'];
 foreach ($aVarPrefixes as $sPrefix) {
 $sFieldName = $sPrefix . \ucfirst($sName);
 if (isset($this->{$sFieldName})) {
 return $this->{$sFieldName};
 }
 }
 return null;
 }
 public function set($aNames, $mValue)
 {
 $aVarPrefixes = ['a', 's', 'm', 'b', 'f', 'o', 'c', 'i'];
 if (\is_string($aNames) && \strpos($aNames, '*') !== \false) {
 $aNames = [\str_replace('*', 'Before', $aNames), \str_replace('*', 'Between', $aNames), \str_replace('*', 'After', $aNames)];
 } elseif (!\is_array($aNames)) {
 $aNames = [$aNames];
 }
 foreach ($aVarPrefixes as $sPrefix) {
 $bDidReplace = \false;
 foreach ($aNames as $sName) {
 $sFieldName = $sPrefix . \ucfirst($sName);
 if (isset($this->{$sFieldName})) {
 $this->{$sFieldName} = $mValue;
 $bDidReplace = \true;
 }
 }
 if ($bDidReplace) {
 return $this;
 }
 }
 // Break the chain so the user knows this option is invalid
 return \false;
 }
 public function __call($sMethodName, array $aArguments)
 {
 if (\strpos($sMethodName, 'set') === 0) {
 return $this->set(\substr($sMethodName, 3), $aArguments[0]);
 } elseif (\strpos($sMethodName, 'get') === 0) {
 return $this->get(\substr($sMethodName, 3));
 } elseif (\method_exists(OutputFormatter::class, $sMethodName)) {
 return \call_user_func_array([$this->getFormatter(), $sMethodName], $aArguments);
 } else {
 throw new \Exception('Unknown OutputFormat method called: ' . $sMethodName);
 }
 }
 public function indentWithTabs($iNumber = 1)
 {
 return $this->setIndentation(\str_repeat("\t", $iNumber));
 }
 public function indentWithSpaces($iNumber = 2)
 {
 return $this->setIndentation(\str_repeat(" ", $iNumber));
 }
 public function nextLevel()
 {
 if ($this->oNextLevelFormat === null) {
 $this->oNextLevelFormat = clone $this;
 $this->oNextLevelFormat->iIndentationLevel++;
 $this->oNextLevelFormat->oFormatter = null;
 }
 return $this->oNextLevelFormat;
 }
 public function beLenient()
 {
 $this->bIgnoreExceptions = \true;
 }
 public function getFormatter()
 {
 if ($this->oFormatter === null) {
 $this->oFormatter = new OutputFormatter($this);
 }
 return $this->oFormatter;
 }
 public function level()
 {
 return $this->iIndentationLevel;
 }
 public static function create()
 {
 return new OutputFormat();
 }
 public static function createCompact()
 {
 $format = self::create();
 $format->set('Space*Rules', "")->set('Space*Blocks', "")->setSpaceAfterRuleName('')->setSpaceBeforeOpeningBrace('')->setSpaceAfterSelectorSeparator('');
 return $format;
 }
 public static function createPretty()
 {
 $format = self::create();
 $format->set('Space*Rules', "\n")->set('Space*Blocks', "\n")->setSpaceBetweenBlocks("\n\n")->set('SpaceAfterListArgumentSeparator', ['default' => '', ',' => ' ']);
 return $format;
 }
}
