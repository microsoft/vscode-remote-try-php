<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class AtLeastOneOf extends Composite
{
 public const AT_LEAST_ONE_OF_ERROR = 'f27e6d6c-261a-4056-b391-6673a623531c';
 protected static $errorNames = [self::AT_LEAST_ONE_OF_ERROR => 'AT_LEAST_ONE_OF_ERROR'];
 public $constraints = [];
 public $message = 'This value should satisfy at least one of the following constraints:';
 public $messageCollection = 'Each element of this collection should satisfy its own set of constraints.';
 public $includeInternalMessages = \true;
 public function __construct($constraints = null, array $groups = null, $payload = null, string $message = null, string $messageCollection = null, bool $includeInternalMessages = null)
 {
 parent::__construct($constraints ?? [], $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->messageCollection = $messageCollection ?? $this->messageCollection;
 $this->includeInternalMessages = $includeInternalMessages ?? $this->includeInternalMessages;
 }
 public function getDefaultOption()
 {
 return 'constraints';
 }
 public function getRequiredOptions()
 {
 return ['constraints'];
 }
 protected function getCompositeOption()
 {
 return 'constraints';
 }
}
