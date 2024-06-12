<?php
namespace MailPoetVendor\Sabberworm\CSS\Property;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\Comment\Comment;
use MailPoetVendor\Sabberworm\CSS\OutputFormat;
use MailPoetVendor\Sabberworm\CSS\Value\URL;
class Import implements AtRule
{
 private $oLocation;
 private $sMediaQuery;
 protected $iLineNo;
 protected $aComments;
 public function __construct(URL $oLocation, $sMediaQuery, $iLineNo = 0)
 {
 $this->oLocation = $oLocation;
 $this->sMediaQuery = $sMediaQuery;
 $this->iLineNo = $iLineNo;
 $this->aComments = [];
 }
 public function getLineNo()
 {
 return $this->iLineNo;
 }
 public function setLocation($oLocation)
 {
 $this->oLocation = $oLocation;
 }
 public function getLocation()
 {
 return $this->oLocation;
 }
 public function __toString()
 {
 return $this->render(new OutputFormat());
 }
 public function render(OutputFormat $oOutputFormat)
 {
 return "@import " . $this->oLocation->render($oOutputFormat) . ($this->sMediaQuery === null ? '' : ' ' . $this->sMediaQuery) . ';';
 }
 public function atRuleName()
 {
 return 'import';
 }
 public function atRuleArgs()
 {
 $aResult = [$this->oLocation];
 if ($this->sMediaQuery) {
 \array_push($aResult, $this->sMediaQuery);
 }
 return $aResult;
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
