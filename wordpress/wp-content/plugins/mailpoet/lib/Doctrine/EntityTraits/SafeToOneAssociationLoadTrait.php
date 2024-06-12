<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\EntityTraits;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\ORM\EntityNotFoundException;
use MailPoetVendor\Doctrine\ORM\Proxy\Proxy;

trait SafeToOneAssociationLoadTrait {
  private function safelyLoadToOneAssociation(string $propertyName, $emptyValue = null) {
    if (!property_exists($this, $propertyName)) {
      throw new \InvalidArgumentException("Property '$propertyName' does not exist on class '" . get_class($this) . "'");
    }

    if (!$this->$propertyName instanceof Proxy) {
      return;
    }

    if ($this->$propertyName->__isInitialized()) {
      return;
    }

    // if a proxy exists (= we have related entity ID), try to load it
    // to see if it is a valid ID referencing an existing entity
    try {
      $this->$propertyName->__load();
    } catch (EntityNotFoundException $e) {
      $this->$propertyName = $emptyValue;
    }
  }
}
