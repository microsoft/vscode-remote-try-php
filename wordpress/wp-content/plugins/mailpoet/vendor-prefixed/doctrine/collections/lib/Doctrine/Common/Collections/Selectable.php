<?php
namespace MailPoetVendor\Doctrine\Common\Collections;
if (!defined('ABSPATH')) exit;
interface Selectable
{
 public function matching(Criteria $criteria);
}
