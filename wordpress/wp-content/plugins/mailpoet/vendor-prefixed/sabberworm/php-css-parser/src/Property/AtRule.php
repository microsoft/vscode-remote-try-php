<?php
namespace MailPoetVendor\Sabberworm\CSS\Property;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\Comment\Commentable;
use MailPoetVendor\Sabberworm\CSS\Renderable;
interface AtRule extends Renderable, Commentable
{
 const BLOCK_RULES = 'media/document/supports/region-style/font-feature-values';
 const SET_RULES = 'font-face/counter-style/page/swash/styleset/annotation';
 public function atRuleName();
 public function atRuleArgs();
}
