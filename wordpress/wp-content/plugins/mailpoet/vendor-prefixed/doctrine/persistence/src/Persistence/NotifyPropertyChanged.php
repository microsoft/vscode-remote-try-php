<?php
namespace MailPoetVendor\Doctrine\Persistence;
if (!defined('ABSPATH')) exit;
interface NotifyPropertyChanged
{
 public function addPropertyChangedListener(PropertyChangedListener $listener);
}
