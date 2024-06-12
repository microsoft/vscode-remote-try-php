<?php
namespace MailPoetVendor\Sabberworm\CSS\Value;
if (!defined('ABSPATH')) exit;
class RuleValueList extends ValueList
{
 public function __construct($sSeparator = ',', $iLineNo = 0)
 {
 parent::__construct([], $sSeparator, $iLineNo);
 }
}
