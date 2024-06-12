<?php
namespace MailPoetVendor\Sabberworm\CSS\Property;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\Comment\Comment;
use MailPoetVendor\Sabberworm\CSS\OutputFormat;
class Charset implements AtRule
{
 private $sCharset;
 protected $iLineNo;
 protected $aComments;
 public function __construct($sCharset, $iLineNo = 0)
 {
 $this->sCharset = $sCharset;
 $this->iLineNo = $iLineNo;
 $this->aComments = [];
 }
 public function getLineNo()
 {
 return $this->iLineNo;
 }
 public function setCharset($sCharset)
 {
 $this->sCharset = $sCharset;
 }
 public function getCharset()
 {
 return $this->sCharset;
 }
 public function __toString()
 {
 return $this->render(new OutputFormat());
 }
 public function render(OutputFormat $oOutputFormat)
 {
 return "@charset {$this->sCharset->render($oOutputFormat)};";
 }
 public function atRuleName()
 {
 return 'charset';
 }
 public function atRuleArgs()
 {
 return $this->sCharset;
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
