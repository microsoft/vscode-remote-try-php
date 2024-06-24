<?php
namespace MailPoetVendor\Sabberworm\CSS\Value;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\OutputFormat;
use MailPoetVendor\Sabberworm\CSS\Parsing\ParserState;
use MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedEOFException;
use MailPoetVendor\Sabberworm\CSS\Parsing\UnexpectedTokenException;
class LineName extends ValueList
{
 public function __construct(array $aComponents = [], $iLineNo = 0)
 {
 parent::__construct($aComponents, ' ', $iLineNo);
 }
 public static function parse(ParserState $oParserState)
 {
 $oParserState->consume('[');
 $oParserState->consumeWhiteSpace();
 $aNames = [];
 do {
 if ($oParserState->getSettings()->bLenientParsing) {
 try {
 $aNames[] = $oParserState->parseIdentifier();
 } catch (UnexpectedTokenException $e) {
 if (!$oParserState->comes(']')) {
 throw $e;
 }
 }
 } else {
 $aNames[] = $oParserState->parseIdentifier();
 }
 $oParserState->consumeWhiteSpace();
 } while (!$oParserState->comes(']'));
 $oParserState->consume(']');
 return new LineName($aNames, $oParserState->currentLine());
 }
 public function __toString()
 {
 return $this->render(new OutputFormat());
 }
 public function render(OutputFormat $oOutputFormat)
 {
 return '[' . parent::render(OutputFormat::createCompact()) . ']';
 }
}
