<?php
namespace MailPoetVendor\Sabberworm\CSS\Property;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\Comment\Comment;
use MailPoetVendor\Sabberworm\CSS\OutputFormat;
use MailPoetVendor\Sabberworm\CSS\Value\CSSString;
class Charset implements AtRule
{
 private $oCharset;
 protected $iLineNo;
 protected $aComments;
 public function __construct(CSSString $oCharset, $iLineNo = 0)
 {
 $this->oCharset = $oCharset;
 $this->iLineNo = $iLineNo;
 $this->aComments = [];
 }
 public function getLineNo()
 {
 return $this->iLineNo;
 }
 public function setCharset($sCharset)
 {
 $sCharset = $sCharset instanceof CSSString ? $sCharset : new CSSString($sCharset);
 $this->oCharset = $sCharset;
 }
 public function getCharset()
 {
 return $this->oCharset->getString();
 }
 public function __toString()
 {
 return $this->render(new OutputFormat());
 }
 public function render(OutputFormat $oOutputFormat)
 {
 return "{$oOutputFormat->comments($this)}@charset {$this->oCharset->render($oOutputFormat)};";
 }
 public function atRuleName()
 {
 return 'charset';
 }
 public function atRuleArgs()
 {
 return $this->oCharset;
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
