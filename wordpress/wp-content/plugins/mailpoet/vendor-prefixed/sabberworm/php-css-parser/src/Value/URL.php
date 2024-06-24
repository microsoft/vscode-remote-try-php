<?php
namespace MailPoetVendor\Sabberworm\CSS\Value;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\OutputFormat;
use MailPoetVendor\Sabberworm\CSS\Parsing\ParserState;
use MailPoetVendor\Sabberworm\CSS\Parsing\SourceException;
use MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedEOFException;
use MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedTokenException;
class URL extends PrimitiveValue
{
 private $oURL;
 public function __construct(CSSString $oURL, $iLineNo = 0)
 {
 parent::__construct($iLineNo);
 $this->oURL = $oURL;
 }
 public static function parse(ParserState $oParserState)
 {
 $oAnchor = $oParserState->anchor();
 $sIdentifier = '';
 for ($i = 0; $i < 3; $i++) {
 $sChar = $oParserState->parseCharacter(\true);
 if ($sChar === null) {
 break;
 }
 $sIdentifier .= $sChar;
 }
 $bUseUrl = $oParserState->streql($sIdentifier, 'url');
 if ($bUseUrl) {
 $oParserState->consumeWhiteSpace();
 $oParserState->consume('(');
 } else {
 $oAnchor->backtrack();
 }
 $oParserState->consumeWhiteSpace();
 $oResult = new URL(CSSString::parse($oParserState), $oParserState->currentLine());
 if ($bUseUrl) {
 $oParserState->consumeWhiteSpace();
 $oParserState->consume(')');
 }
 return $oResult;
 }
 public function setURL(CSSString $oURL)
 {
 $this->oURL = $oURL;
 }
 public function getURL()
 {
 return $this->oURL;
 }
 public function __toString()
 {
 return $this->render(new OutputFormat());
 }
 public function render(OutputFormat $oOutputFormat)
 {
 return "url({$this->oURL->render($oOutputFormat)})";
 }
}
