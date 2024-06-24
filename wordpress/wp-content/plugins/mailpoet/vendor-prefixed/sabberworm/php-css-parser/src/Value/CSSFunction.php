<?php
namespace MailPoetVendor\Sabberworm\CSS\Value;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\OutputFormat;
use MailPoetVendor\Sabberworm\CSS\Parsing\ParserState;
class CSSFunction extends ValueList
{
 protected $sName;
 public function __construct($sName, $aArguments, $sSeparator = ',', $iLineNo = 0)
 {
 if ($aArguments instanceof RuleValueList) {
 $sSeparator = $aArguments->getListSeparator();
 $aArguments = $aArguments->getListComponents();
 }
 $this->sName = $sName;
 $this->iLineNo = $iLineNo;
 parent::__construct($aArguments, $sSeparator, $iLineNo);
 }
 public static function parse(ParserState $oParserState, $bIgnoreCase = \false)
 {
 $mResult = $oParserState->parseIdentifier($bIgnoreCase);
 $oParserState->consume('(');
 $aArguments = Value::parseValue($oParserState, ['=', ' ', ',']);
 $mResult = new CSSFunction($mResult, $aArguments, ',', $oParserState->currentLine());
 $oParserState->consume(')');
 return $mResult;
 }
 public function getName()
 {
 return $this->sName;
 }
 public function setName($sName)
 {
 $this->sName = $sName;
 }
 public function getArguments()
 {
 return $this->aComponents;
 }
 public function __toString()
 {
 return $this->render(new OutputFormat());
 }
 public function render(OutputFormat $oOutputFormat)
 {
 $aArguments = parent::render($oOutputFormat);
 return "{$this->sName}({$aArguments})";
 }
}
