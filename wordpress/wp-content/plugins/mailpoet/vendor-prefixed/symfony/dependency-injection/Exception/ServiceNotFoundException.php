<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Container\NotFoundExceptionInterface;
class ServiceNotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
 private $id;
 private $sourceId;
 private $alternatives;
 public function __construct(string $id, ?string $sourceId = null, ?\Throwable $previous = null, array $alternatives = [], ?string $msg = null)
 {
 if (null !== $msg) {
 // no-op
 } elseif (null === $sourceId) {
 $msg = \sprintf('You have requested a non-existent service "%s".', $id);
 } else {
 $msg = \sprintf('The service "%s" has a dependency on a non-existent service "%s".', $sourceId, $id);
 }
 if ($alternatives) {
 if (1 == \count($alternatives)) {
 $msg .= ' Did you mean this: "';
 } else {
 $msg .= ' Did you mean one of these: "';
 }
 $msg .= \implode('", "', $alternatives) . '"?';
 }
 parent::__construct($msg, 0, $previous);
 $this->id = $id;
 $this->sourceId = $sourceId;
 $this->alternatives = $alternatives;
 }
 public function getId()
 {
 return $this->id;
 }
 public function getSourceId()
 {
 return $this->sourceId;
 }
 public function getAlternatives()
 {
 return $this->alternatives;
 }
}
