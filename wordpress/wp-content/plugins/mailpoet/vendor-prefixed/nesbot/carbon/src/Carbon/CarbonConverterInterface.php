<?php
namespace MailPoetVendor\Carbon;
if (!defined('ABSPATH')) exit;
use DateTimeInterface;
interface CarbonConverterInterface
{
 public function convertDate(DateTimeInterface $dateTime, bool $negated = \false) : CarbonInterface;
}
