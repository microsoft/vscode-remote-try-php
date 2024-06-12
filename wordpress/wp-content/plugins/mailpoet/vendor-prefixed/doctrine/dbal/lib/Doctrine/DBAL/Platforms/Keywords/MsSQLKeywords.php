<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms\Keywords;
if (!defined('ABSPATH')) exit;
class MsSQLKeywords extends SQLServerKeywords
{
 public function getName()
 {
 return 'MsSQL';
 }
}
