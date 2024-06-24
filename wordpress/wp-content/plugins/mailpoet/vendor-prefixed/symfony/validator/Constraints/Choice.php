<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Choice extends Constraint
{
 public const NO_SUCH_CHOICE_ERROR = '8e179f1b-97aa-4560-a02f-2a8b42e49df7';
 public const TOO_FEW_ERROR = '11edd7eb-5872-4b6e-9f12-89923999fd0e';
 public const TOO_MANY_ERROR = '9bd98e49-211c-433f-8630-fd1c2d0f08c3';
 protected static $errorNames = [self::NO_SUCH_CHOICE_ERROR => 'NO_SUCH_CHOICE_ERROR', self::TOO_FEW_ERROR => 'TOO_FEW_ERROR', self::TOO_MANY_ERROR => 'TOO_MANY_ERROR'];
 public $choices;
 public $callback;
 public $multiple = \false;
 public $strict = \true;
 public $min;
 public $max;
 public $message = 'The value you selected is not a valid choice.';
 public $multipleMessage = 'One or more of the given values is invalid.';
 public $minMessage = 'You must select at least {{ limit }} choice.|You must select at least {{ limit }} choices.';
 public $maxMessage = 'You must select at most {{ limit }} choice.|You must select at most {{ limit }} choices.';
 public function getDefaultOption()
 {
 return 'choices';
 }
 public function __construct($options = [], array $choices = null, $callback = null, bool $multiple = null, bool $strict = null, int $min = null, int $max = null, string $message = null, string $multipleMessage = null, string $minMessage = null, string $maxMessage = null, $groups = null, $payload = null)
 {
 if (\is_array($options) && $options && \array_is_list($options)) {
 $choices = $choices ?? $options;
 $options = [];
 }
 if (null !== $choices) {
 $options['value'] = $choices;
 }
 parent::__construct($options, $groups, $payload);
 $this->callback = $callback ?? $this->callback;
 $this->multiple = $multiple ?? $this->multiple;
 $this->strict = $strict ?? $this->strict;
 $this->min = $min ?? $this->min;
 $this->max = $max ?? $this->max;
 $this->message = $message ?? $this->message;
 $this->multipleMessage = $multipleMessage ?? $this->multipleMessage;
 $this->minMessage = $minMessage ?? $this->minMessage;
 $this->maxMessage = $maxMessage ?? $this->maxMessage;
 }
}
