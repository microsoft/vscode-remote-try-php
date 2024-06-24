<?php
namespace MailPoetVendor\Sabberworm\CSS;
if (!defined('ABSPATH')) exit;
class Settings
{
 public $bMultibyteSupport;
 public $sDefaultCharset = 'utf-8';
 public $bLenientParsing = \true;
 private function __construct()
 {
 $this->bMultibyteSupport = \extension_loaded('mbstring');
 }
 public static function create()
 {
 return new Settings();
 }
 public function withMultibyteSupport($bMultibyteSupport = \true)
 {
 $this->bMultibyteSupport = $bMultibyteSupport;
 return $this;
 }
 public function withDefaultCharset($sDefaultCharset)
 {
 $this->sDefaultCharset = $sDefaultCharset;
 return $this;
 }
 public function withLenientParsing($bLenientParsing = \true)
 {
 $this->bLenientParsing = $bLenientParsing;
 return $this;
 }
 public function beStrict()
 {
 return $this->withLenientParsing(\false);
 }
}
