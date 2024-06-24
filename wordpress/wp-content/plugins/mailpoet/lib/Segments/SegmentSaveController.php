<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments;

if (!defined('ABSPATH')) exit;


use MailPoet\ConflictException;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\NotFoundException;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\ORMException;

class SegmentSaveController {
  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    SegmentsRepository $segmentsRepository,
    EntityManager $entityManager
  ) {
    $this->segmentsRepository = $segmentsRepository;
    $this->entityManager = $entityManager;
  }

  /**
   * @throws ConflictException
   * @throws NotFoundException
   * @throws ORMException
   */
  public function save(array $data = []): SegmentEntity {
    $id = isset($data['id']) ? (int)$data['id'] : null;
    $name = $data['name'] ?? '';
    $description = $data['description'] ?? '';
    $displayInManageSubPage = isset($data['showInManageSubscriptionPage']) ? (int)$data['showInManageSubscriptionPage'] : false;

    return $this->segmentsRepository->createOrUpdate($name, $description, SegmentEntity::TYPE_DEFAULT, [], $id, (bool)$displayInManageSubPage);
  }

  /**
   * @throws ConflictException
   */
  public function duplicate(SegmentEntity $segmentEntity): SegmentEntity {
    $duplicate = clone $segmentEntity;
    // translators: %s is the name of the segment
    $duplicate->setName(sprintf(__('Copy of %s', 'mailpoet'), $segmentEntity->getName()));

    $this->segmentsRepository->verifyNameIsUnique($duplicate->getName(), $duplicate->getId());

    $this->entityManager->transactional(function (EntityManager $entityManager) use ($duplicate, $segmentEntity) {
      $entityManager->persist($duplicate);
      $entityManager->flush();

      $subscriberSegmentTable = $entityManager->getClassMetadata(SubscriberSegmentEntity::class)->getTableName();
      $conn = $this->entityManager->getConnection();
      $stmt = $conn->prepare("
        INSERT INTO $subscriberSegmentTable (segment_id, subscriber_id, status, created_at)
        SELECT :duplicateId, subscriber_id, status, NOW()
        FROM $subscriberSegmentTable
        WHERE segment_id = :segmentId
      ");
      $stmt->executeQuery([
        'duplicateId' => $duplicate->getId(),
        'segmentId' => $segmentEntity->getId(),
      ]);
    });

    return $duplicate;
  }
}
