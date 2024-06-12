<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Profiler\NodeVisitor\ProfilerNodeVisitor;
use MailPoetVendor\Twig\Profiler\Profile;
class ProfilerExtension extends AbstractExtension
{
 private $actives = [];
 public function __construct(Profile $profile)
 {
 $this->actives[] = $profile;
 }
 public function enter(Profile $profile)
 {
 $this->actives[0]->addProfile($profile);
 \array_unshift($this->actives, $profile);
 }
 public function leave(Profile $profile)
 {
 $profile->leave();
 \array_shift($this->actives);
 if (1 === \count($this->actives)) {
 $this->actives[0]->leave();
 }
 }
 public function getNodeVisitors() : array
 {
 return [new ProfilerNodeVisitor(static::class)];
 }
}
