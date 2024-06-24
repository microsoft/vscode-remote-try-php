<?php
namespace MailPoetVendor\Symfony\Contracts\Service;
if (!defined('ABSPATH')) exit;
interface ServiceSubscriberInterface
{
 public static function getSubscribedServices();
}
