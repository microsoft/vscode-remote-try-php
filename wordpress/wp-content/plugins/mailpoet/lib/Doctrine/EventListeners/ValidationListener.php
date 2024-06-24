<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\EventListeners;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Validator\ValidationException;
use MailPoetVendor\Doctrine\ORM\Event\OnFlushEventArgs;
use MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationListener {
  /** @var ValidatorInterface */
  private $validator;

  public function __construct(
    ValidatorInterface $validator
  ) {
    $this->validator = $validator;
  }

  public function onFlush(OnFlushEventArgs $eventArgs) {
    $unitOfWork = $eventArgs->getEntityManager()->getUnitOfWork();

    foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
      $this->validate($entity);
    }

    foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
      $this->validate($entity);
    }
  }

  private function validate($entity) {
    $groups = $this->getValidationGroups($entity);
    $violations = $this->validator->validate($entity, null, $groups);
    if ($violations->count() > 0) {
      throw new ValidationException(get_class($entity), $violations);
    }
  }

  private function getValidationGroups($entity) {
    if (is_object($entity) && method_exists($entity, 'getValidationGroups')) {
      return $entity->getValidationGroups();
    }
    return null;
  }
}
