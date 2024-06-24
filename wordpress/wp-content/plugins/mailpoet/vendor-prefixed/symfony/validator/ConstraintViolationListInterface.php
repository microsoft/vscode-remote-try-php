<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
interface ConstraintViolationListInterface extends \Traversable, \Countable, \ArrayAccess
{
 public function add(ConstraintViolationInterface $violation);
 public function addAll(self $otherList);
 public function get(int $offset);
 public function has(int $offset);
 public function set(int $offset, ConstraintViolationInterface $violation);
 public function remove(int $offset);
}
