<?php
namespace MailPoetVendor\Sabberworm\CSS\Value;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\OutputFormat;
class CalcRuleValueList extends RuleValueList
{
 public function __construct($iLineNo = 0)
 {
 parent::__construct(',', $iLineNo);
 }
 public function render(OutputFormat $oOutputFormat)
 {
 return $oOutputFormat->implode(' ', $this->aComponents);
 }
}
