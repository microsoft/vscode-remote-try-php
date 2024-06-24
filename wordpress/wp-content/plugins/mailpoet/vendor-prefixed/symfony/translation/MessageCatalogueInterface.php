<?php
namespace MailPoetVendor\Symfony\Component\Translation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Config\Resource\ResourceInterface;
interface MessageCatalogueInterface
{
 public const INTL_DOMAIN_SUFFIX = '+intl-icu';
 public function getLocale();
 public function getDomains();
 public function all($domain = null);
 public function set($id, $translation, $domain = 'messages');
 public function has($id, $domain = 'messages');
 public function defines($id, $domain = 'messages');
 public function get($id, $domain = 'messages');
 public function replace($messages, $domain = 'messages');
 public function add($messages, $domain = 'messages');
 public function addCatalogue(self $catalogue);
 public function addFallbackCatalogue(self $catalogue);
 public function getFallbackCatalogue();
 public function getResources();
 public function addResource(ResourceInterface $resource);
}
