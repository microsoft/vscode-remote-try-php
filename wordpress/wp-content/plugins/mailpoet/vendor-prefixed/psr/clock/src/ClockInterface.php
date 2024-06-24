<?php
namespace MailPoetVendor\Psr\Clock;
if (!defined('ABSPATH')) exit;
use DateTimeImmutable;
interface ClockInterface
{
 public function now() : DateTimeImmutable;
}
