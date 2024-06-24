<?php
namespace MailPoetVendor\Doctrine\Persistence;
if (!defined('ABSPATH')) exit;
use UnexpectedValueException;
interface ObjectRepository
{
 public function find($id);
 public function findAll();
 public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null);
 public function findOneBy(array $criteria);
 public function getClassName();
}
