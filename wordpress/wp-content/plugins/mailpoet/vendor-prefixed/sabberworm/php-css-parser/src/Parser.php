<?php
namespace MailPoetVendor\Sabberworm\CSS;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\CSSList\Document;
use MailPoetVendor\Sabberworm\CSS\Parsing\ParserState;
use MailPoetVendor\Sabberworm\CSS\Parsing\SourceException;
class Parser
{
 private $oParserState;
 public function __construct($sText, Settings $oParserSettings = null, $iLineNo = 1)
 {
 if ($oParserSettings === null) {
 $oParserSettings = Settings::create();
 }
 $this->oParserState = new ParserState($sText, $oParserSettings, $iLineNo);
 }
 public function setCharset($sCharset)
 {
 $this->oParserState->setCharset($sCharset);
 }
 public function getCharset()
 {
 // Note: The `return` statement is missing here. This is a bug that needs to be fixed.
 $this->oParserState->getCharset();
 }
 public function parse()
 {
 return Document::parse($this->oParserState);
 }
}
