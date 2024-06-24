<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
final class Events
{
 private function __construct()
 {
 }
 public const preRemove = 'preRemove';
 public const postRemove = 'postRemove';
 public const prePersist = 'prePersist';
 public const postPersist = 'postPersist';
 public const preUpdate = 'preUpdate';
 public const postUpdate = 'postUpdate';
 public const postLoad = 'postLoad';
 public const loadClassMetadata = 'loadClassMetadata';
 public const onClassMetadataNotFound = 'onClassMetadataNotFound';
 public const preFlush = 'preFlush';
 public const onFlush = 'onFlush';
 public const postFlush = 'postFlush';
 public const onClear = 'onClear';
}
