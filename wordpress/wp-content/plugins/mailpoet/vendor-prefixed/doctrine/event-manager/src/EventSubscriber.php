<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\Common;
if (!defined('ABSPATH')) exit;
interface EventSubscriber
{
 public function getSubscribedEvents();
}
