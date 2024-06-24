<?php
namespace MailPoetVendor\Gregwar\Captcha;
if (!defined('ABSPATH')) exit;
interface CaptchaBuilderInterface
{
 public function build($width, $height, $font, $fingerprint);
 public function save($filename, $quality);
 public function get($quality);
 public function output($quality);
}
