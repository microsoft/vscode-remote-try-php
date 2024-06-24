<?php
namespace MailPoetVendor\Doctrine\Persistence;
if (!defined('ABSPATH')) exit;
interface Proxy
{
 public const MARKER = '__CG__';
 public const MARKER_LENGTH = 6;
 public function __load();
 public function __isInitialized();
}
