<?php
namespace MailPoetVendor\Sabberworm\CSS\CSSList;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\OutputFormat;
use MailPoetVendor\Sabberworm\CSS\Property\AtRule;
class AtRuleBlockList extends CSSBlockList implements AtRule
{
 private $sType;
 private $sArgs;
 public function __construct($sType, $sArgs = '', $iLineNo = 0)
 {
 parent::__construct($iLineNo);
 $this->sType = $sType;
 $this->sArgs = $sArgs;
 }
 public function atRuleName()
 {
 return $this->sType;
 }
 public function atRuleArgs()
 {
 return $this->sArgs;
 }
 public function __toString()
 {
 return $this->render(new OutputFormat());
 }
 public function render(OutputFormat $oOutputFormat)
 {
 $sResult = $oOutputFormat->comments($this);
 $sResult .= $oOutputFormat->sBeforeAtRuleBlock;
 $sArgs = $this->sArgs;
 if ($sArgs) {
 $sArgs = ' ' . $sArgs;
 }
 $sResult .= "@{$this->sType}{$sArgs}{$oOutputFormat->spaceBeforeOpeningBrace()}{";
 $sResult .= $this->renderListContents($oOutputFormat);
 $sResult .= '}';
 $sResult .= $oOutputFormat->sAfterAtRuleBlock;
 return $sResult;
 }
 public function isRootList()
 {
 return \false;
 }
}
