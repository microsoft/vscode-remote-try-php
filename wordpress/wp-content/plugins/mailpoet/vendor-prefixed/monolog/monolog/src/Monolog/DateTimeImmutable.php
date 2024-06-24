<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog;
if (!defined('ABSPATH')) exit;
use DateTimeZone;
class DateTimeImmutable extends \DateTimeImmutable implements \JsonSerializable
{
 private $useMicroseconds;
 public function __construct(bool $useMicroseconds, ?DateTimeZone $timezone = null)
 {
 $this->useMicroseconds = $useMicroseconds;
 // if you like to use a custom time to pass to Logger::addRecord directly,
 // call modify() or setTimestamp() on this instance to change the date after creating it
 parent::__construct('now', $timezone);
 }
 public function jsonSerialize() : string
 {
 if ($this->useMicroseconds) {
 return $this->format('Y-m-d\\TH:i:s.uP');
 }
 return $this->format('Y-m-d\\TH:i:sP');
 }
 public function __toString() : string
 {
 return $this->jsonSerialize();
 }
}
