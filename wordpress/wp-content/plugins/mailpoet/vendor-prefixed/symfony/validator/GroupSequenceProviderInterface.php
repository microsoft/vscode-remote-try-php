<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequence;
interface GroupSequenceProviderInterface
{
 public function getGroupSequence();
}
