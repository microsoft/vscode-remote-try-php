<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\SubjectTransformers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Integration\SubjectTransformer;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\CustomerSubject;
use MailPoet\Automation\Integrations\WordPress\Subjects\UserSubject;

class WordPressUserSubjectToWooCommerceCustomerSubjectTransformer implements SubjectTransformer {
  public function accepts(): string {
    return UserSubject::KEY;
  }

  public function returns(): string {
    return CustomerSubject::KEY;
  }

  public function transform(Subject $data): Subject {
    if ($this->accepts() !== $data->getKey()) {
      throw new \InvalidArgumentException('Invalid subject type');
    }
    return new Subject(CustomerSubject::KEY, ['customer_id' => $data->getArgs()['user_id']]);
  }
}
