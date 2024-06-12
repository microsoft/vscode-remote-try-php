<?php
namespace MailPoetVendor\Sabberworm\CSS\Value;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\OutputFormat;
use MailPoetVendor\Sabberworm\CSS\Parsing\ParserState;
use MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedEOFException;
use MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedTokenException;
class Size extends PrimitiveValue
{
 const ABSOLUTE_SIZE_UNITS = ['px', 'cm', 'mm', 'mozmm', 'in', 'pt', 'pc', 'vh', 'vw', 'vmin', 'vmax', 'rem'];
 const RELATIVE_SIZE_UNITS = ['%', 'em', 'ex', 'ch', 'fr'];
 const NON_SIZE_UNITS = ['deg', 'grad', 'rad', 's', 'ms', 'turns', 'Hz', 'kHz'];
 private static $SIZE_UNITS = null;
 private $fSize;
 private $sUnit;
 private $bIsColorComponent;
 public function __construct($fSize, $sUnit = null, $bIsColorComponent = \false, $iLineNo = 0)
 {
 parent::__construct($iLineNo);
 $this->fSize = (float) $fSize;
 $this->sUnit = $sUnit;
 $this->bIsColorComponent = $bIsColorComponent;
 }
 public static function parse(ParserState $oParserState, $bIsColorComponent = \false)
 {
 $sSize = '';
 if ($oParserState->comes('-')) {
 $sSize .= $oParserState->consume('-');
 }
 while (\is_numeric($oParserState->peek()) || $oParserState->comes('.')) {
 if ($oParserState->comes('.')) {
 $sSize .= $oParserState->consume('.');
 } else {
 $sSize .= $oParserState->consume(1);
 }
 }
 $sUnit = null;
 $aSizeUnits = self::getSizeUnits();
 foreach ($aSizeUnits as $iLength => &$aValues) {
 $sKey = \strtolower($oParserState->peek($iLength));
 if (\array_key_exists($sKey, $aValues)) {
 if (($sUnit = $aValues[$sKey]) !== null) {
 $oParserState->consume($iLength);
 break;
 }
 }
 }
 return new Size((float) $sSize, $sUnit, $bIsColorComponent, $oParserState->currentLine());
 }
 private static function getSizeUnits()
 {
 if (!\is_array(self::$SIZE_UNITS)) {
 self::$SIZE_UNITS = [];
 foreach (\array_merge(self::ABSOLUTE_SIZE_UNITS, self::RELATIVE_SIZE_UNITS, self::NON_SIZE_UNITS) as $val) {
 $iSize = \strlen($val);
 if (!isset(self::$SIZE_UNITS[$iSize])) {
 self::$SIZE_UNITS[$iSize] = [];
 }
 self::$SIZE_UNITS[$iSize][\strtolower($val)] = $val;
 }
 \krsort(self::$SIZE_UNITS, \SORT_NUMERIC);
 }
 return self::$SIZE_UNITS;
 }
 public function setUnit($sUnit)
 {
 $this->sUnit = $sUnit;
 }
 public function getUnit()
 {
 return $this->sUnit;
 }
 public function setSize($fSize)
 {
 $this->fSize = (float) $fSize;
 }
 public function getSize()
 {
 return $this->fSize;
 }
 public function isColorComponent()
 {
 return $this->bIsColorComponent;
 }
 public function isSize()
 {
 if (\in_array($this->sUnit, self::NON_SIZE_UNITS, \true)) {
 return \false;
 }
 return !$this->isColorComponent();
 }
 public function isRelative()
 {
 if (\in_array($this->sUnit, self::RELATIVE_SIZE_UNITS, \true)) {
 return \true;
 }
 if ($this->sUnit === null && $this->fSize != 0) {
 return \true;
 }
 return \false;
 }
 public function __toString()
 {
 return $this->render(new OutputFormat());
 }
 public function render(OutputFormat $oOutputFormat)
 {
 $l = \localeconv();
 $sPoint = \preg_quote($l['decimal_point'], '/');
 $sSize = \preg_match("/[\\d\\.]+e[+-]?\\d+/i", (string) $this->fSize) ? \preg_replace("/{$sPoint}?0+\$/", "", \sprintf("%f", $this->fSize)) : $this->fSize;
 return \preg_replace(["/{$sPoint}/", "/^(-?)0\\./"], ['.', '$1.'], $sSize) . ($this->sUnit === null ? '' : $this->sUnit);
 }
}
