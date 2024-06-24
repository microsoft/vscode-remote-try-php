<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
final class FetchUtils
{
 public static function fetchOne(Result $result)
 {
 $row = $result->fetchNumeric();
 if ($row === \false) {
 return \false;
 }
 return $row[0];
 }
 public static function fetchAllNumeric(Result $result) : array
 {
 $rows = [];
 while (($row = $result->fetchNumeric()) !== \false) {
 $rows[] = $row;
 }
 return $rows;
 }
 public static function fetchAllAssociative(Result $result) : array
 {
 $rows = [];
 while (($row = $result->fetchAssociative()) !== \false) {
 $rows[] = $row;
 }
 return $rows;
 }
 public static function fetchFirstColumn(Result $result) : array
 {
 $rows = [];
 while (($row = $result->fetchOne()) !== \false) {
 $rows[] = $row;
 }
 return $rows;
 }
}
