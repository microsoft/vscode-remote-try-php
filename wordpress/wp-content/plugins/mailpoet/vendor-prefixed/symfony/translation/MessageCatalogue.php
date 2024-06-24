<?php
namespace MailPoetVendor\Symfony\Component\Translation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Config\Resource\ResourceInterface;
use MailPoetVendor\Symfony\Component\Translation\Exception\LogicException;
class MessageCatalogue implements MessageCatalogueInterface, MetadataAwareInterface
{
 private $messages = [];
 private $metadata = [];
 private $resources = [];
 private $locale;
 private $fallbackCatalogue;
 private $parent;
 public function __construct(?string $locale, array $messages = [])
 {
 if (null === $locale) {
 @\trigger_error(\sprintf('Passing "null" to the first argument of the "%s" method has been deprecated since Symfony 4.4 and will throw an error in 5.0.', __METHOD__), \E_USER_DEPRECATED);
 }
 $this->locale = $locale;
 $this->messages = $messages;
 }
 public function getLocale()
 {
 return $this->locale;
 }
 public function getDomains()
 {
 $domains = [];
 foreach ($this->messages as $domain => $messages) {
 if (\str_ends_with($domain, self::INTL_DOMAIN_SUFFIX)) {
 $domain = \substr($domain, 0, -\strlen(self::INTL_DOMAIN_SUFFIX));
 }
 $domains[$domain] = $domain;
 }
 return \array_values($domains);
 }
 public function all($domain = null)
 {
 if (null !== $domain) {
 // skip messages merge if intl-icu requested explicitly
 if (\str_ends_with($domain, self::INTL_DOMAIN_SUFFIX)) {
 return $this->messages[$domain] ?? [];
 }
 return ($this->messages[$domain . self::INTL_DOMAIN_SUFFIX] ?? []) + ($this->messages[$domain] ?? []);
 }
 $allMessages = [];
 foreach ($this->messages as $domain => $messages) {
 if (\str_ends_with($domain, self::INTL_DOMAIN_SUFFIX)) {
 $domain = \substr($domain, 0, -\strlen(self::INTL_DOMAIN_SUFFIX));
 $allMessages[$domain] = $messages + ($allMessages[$domain] ?? []);
 } else {
 $allMessages[$domain] = ($allMessages[$domain] ?? []) + $messages;
 }
 }
 return $allMessages;
 }
 public function set($id, $translation, $domain = 'messages')
 {
 $this->add([$id => $translation], $domain);
 }
 public function has($id, $domain = 'messages')
 {
 if (isset($this->messages[$domain][$id]) || isset($this->messages[$domain . self::INTL_DOMAIN_SUFFIX][$id])) {
 return \true;
 }
 if (null !== $this->fallbackCatalogue) {
 return $this->fallbackCatalogue->has($id, $domain);
 }
 return \false;
 }
 public function defines($id, $domain = 'messages')
 {
 return isset($this->messages[$domain][$id]) || isset($this->messages[$domain . self::INTL_DOMAIN_SUFFIX][$id]);
 }
 public function get($id, $domain = 'messages')
 {
 if (isset($this->messages[$domain . self::INTL_DOMAIN_SUFFIX][$id])) {
 return $this->messages[$domain . self::INTL_DOMAIN_SUFFIX][$id];
 }
 if (isset($this->messages[$domain][$id])) {
 return $this->messages[$domain][$id];
 }
 if (null !== $this->fallbackCatalogue) {
 return $this->fallbackCatalogue->get($id, $domain);
 }
 return $id;
 }
 public function replace($messages, $domain = 'messages')
 {
 unset($this->messages[$domain], $this->messages[$domain . self::INTL_DOMAIN_SUFFIX]);
 $this->add($messages, $domain);
 }
 public function add($messages, $domain = 'messages')
 {
 if (!isset($this->messages[$domain])) {
 $this->messages[$domain] = [];
 }
 $intlDomain = $domain;
 if (!\str_ends_with($domain, self::INTL_DOMAIN_SUFFIX)) {
 $intlDomain .= self::INTL_DOMAIN_SUFFIX;
 }
 foreach ($messages as $id => $message) {
 if (isset($this->messages[$intlDomain]) && \array_key_exists($id, $this->messages[$intlDomain])) {
 $this->messages[$intlDomain][$id] = $message;
 } else {
 $this->messages[$domain][$id] = $message;
 }
 }
 }
 public function addCatalogue(MessageCatalogueInterface $catalogue)
 {
 if ($catalogue->getLocale() !== $this->locale) {
 throw new LogicException(\sprintf('Cannot add a catalogue for locale "%s" as the current locale for this catalogue is "%s".', $catalogue->getLocale(), $this->locale));
 }
 foreach ($catalogue->all() as $domain => $messages) {
 if ($intlMessages = $catalogue->all($domain . self::INTL_DOMAIN_SUFFIX)) {
 $this->add($intlMessages, $domain . self::INTL_DOMAIN_SUFFIX);
 $messages = \array_diff_key($messages, $intlMessages);
 }
 $this->add($messages, $domain);
 }
 foreach ($catalogue->getResources() as $resource) {
 $this->addResource($resource);
 }
 if ($catalogue instanceof MetadataAwareInterface) {
 $metadata = $catalogue->getMetadata('', '');
 $this->addMetadata($metadata);
 }
 }
 public function addFallbackCatalogue(MessageCatalogueInterface $catalogue)
 {
 // detect circular references
 $c = $catalogue;
 while ($c = $c->getFallbackCatalogue()) {
 if ($c->getLocale() === $this->getLocale()) {
 throw new LogicException(\sprintf('Circular reference detected when adding a fallback catalogue for locale "%s".', $catalogue->getLocale()));
 }
 }
 $c = $this;
 do {
 if ($c->getLocale() === $catalogue->getLocale()) {
 throw new LogicException(\sprintf('Circular reference detected when adding a fallback catalogue for locale "%s".', $catalogue->getLocale()));
 }
 foreach ($catalogue->getResources() as $resource) {
 $c->addResource($resource);
 }
 } while ($c = $c->parent);
 $catalogue->parent = $this;
 $this->fallbackCatalogue = $catalogue;
 foreach ($catalogue->getResources() as $resource) {
 $this->addResource($resource);
 }
 }
 public function getFallbackCatalogue()
 {
 return $this->fallbackCatalogue;
 }
 public function getResources()
 {
 return \array_values($this->resources);
 }
 public function addResource(ResourceInterface $resource)
 {
 $this->resources[$resource->__toString()] = $resource;
 }
 public function getMetadata($key = '', $domain = 'messages')
 {
 if ('' == $domain) {
 return $this->metadata;
 }
 if (isset($this->metadata[$domain])) {
 if ('' == $key) {
 return $this->metadata[$domain];
 }
 if (isset($this->metadata[$domain][$key])) {
 return $this->metadata[$domain][$key];
 }
 }
 return null;
 }
 public function setMetadata($key, $value, $domain = 'messages')
 {
 $this->metadata[$domain][$key] = $value;
 }
 public function deleteMetadata($key = '', $domain = 'messages')
 {
 if ('' == $domain) {
 $this->metadata = [];
 } elseif ('' == $key) {
 unset($this->metadata[$domain]);
 } else {
 unset($this->metadata[$domain][$key]);
 }
 }
 private function addMetadata(array $values)
 {
 foreach ($values as $domain => $keys) {
 foreach ($keys as $key => $value) {
 $this->setMetadata($key, $value, $domain);
 }
 }
 }
}
