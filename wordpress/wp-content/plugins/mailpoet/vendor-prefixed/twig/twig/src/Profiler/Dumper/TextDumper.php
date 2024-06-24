<?php
namespace MailPoetVendor\Twig\Profiler\Dumper;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Profiler\Profile;
final class TextDumper extends BaseDumper
{
 protected function formatTemplate(Profile $profile, $prefix) : string
 {
 return \sprintf('%s└ %s', $prefix, $profile->getTemplate());
 }
 protected function formatNonTemplate(Profile $profile, $prefix) : string
 {
 return \sprintf('%s└ %s::%s(%s)', $prefix, $profile->getTemplate(), $profile->getType(), $profile->getName());
 }
 protected function formatTime(Profile $profile, $percent) : string
 {
 return \sprintf('%.2fms/%.0f%%', $profile->getDuration() * 1000, $percent);
 }
}
