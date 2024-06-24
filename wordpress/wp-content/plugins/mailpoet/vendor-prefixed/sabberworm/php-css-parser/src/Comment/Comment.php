<?php
namespace MailPoetVendor\Sabberworm\CSS\Comment;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\OutputFormat;
use MailPoetVendor\Sabberworm\CSS\Renderable;
class Comment implements Renderable
{
 protected $iLineNo;
 protected $sComment;
 public function __construct($sComment = '', $iLineNo = 0)
 {
 $this->sComment = $sComment;
 $this->iLineNo = $iLineNo;
 }
 public function getComment()
 {
 return $this->sComment;
 }
 public function getLineNo()
 {
 return $this->iLineNo;
 }
 public function setComment($sComment)
 {
 $this->sComment = $sComment;
 }
 public function __toString()
 {
 return $this->render(new OutputFormat());
 }
 public function render(OutputFormat $oOutputFormat)
 {
 return '/*' . $this->sComment . '*/';
 }
}
