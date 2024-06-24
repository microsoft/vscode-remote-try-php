<?php
namespace MailPoetVendor\Sabberworm\CSS\Comment;
if (!defined('ABSPATH')) exit;
interface Commentable
{
 public function addComments(array $aComments);
 public function getComments();
 public function setComments(array $aComments);
}
