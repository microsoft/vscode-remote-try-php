<?php
namespace MailPoetVendor\Sabberworm\CSS\Parsing;
if (!defined('ABSPATH')) exit;
class OutputException extends SourceException
{
 public function __construct($sMessage, $iLineNo = 0)
 {
 parent::__construct($sMessage, $iLineNo);
 }
}
