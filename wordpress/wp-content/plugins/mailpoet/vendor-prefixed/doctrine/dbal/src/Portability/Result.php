<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Portability;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Middleware\AbstractResultMiddleware;
use MailPoetVendor\Doctrine\DBAL\Driver\Result as ResultInterface;
final class Result extends AbstractResultMiddleware
{
 private Converter $converter;
 public function __construct(ResultInterface $result, Converter $converter)
 {
 parent::__construct($result);
 $this->converter = $converter;
 }
 public function fetchNumeric()
 {
 return $this->converter->convertNumeric(parent::fetchNumeric());
 }
 public function fetchAssociative()
 {
 return $this->converter->convertAssociative(parent::fetchAssociative());
 }
 public function fetchOne()
 {
 return $this->converter->convertOne(parent::fetchOne());
 }
 public function fetchAllNumeric() : array
 {
 return $this->converter->convertAllNumeric(parent::fetchAllNumeric());
 }
 public function fetchAllAssociative() : array
 {
 return $this->converter->convertAllAssociative(parent::fetchAllAssociative());
 }
 public function fetchFirstColumn() : array
 {
 return $this->converter->convertFirstColumn(parent::fetchFirstColumn());
 }
}
