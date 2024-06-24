<?php
namespace MailPoetVendor\Doctrine\Common;
if (!defined('ABSPATH')) exit;
interface Comparable
{
 public function compareTo($other);
}
