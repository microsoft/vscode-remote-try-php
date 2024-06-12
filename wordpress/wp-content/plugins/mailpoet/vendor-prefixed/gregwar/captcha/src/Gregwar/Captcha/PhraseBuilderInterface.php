<?php
namespace MailPoetVendor\Gregwar\Captcha;
if (!defined('ABSPATH')) exit;
interface PhraseBuilderInterface
{
 public function build();
 public function niceize($str);
}
