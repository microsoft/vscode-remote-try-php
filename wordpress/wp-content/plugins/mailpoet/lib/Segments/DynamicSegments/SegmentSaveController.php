<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments\DynamicSegments;

if (!defined('ABSPATH')) exit;


use MailPoet\ConflictException;
use MailPoet\Entities\SegmentEntity;
use MailPoet\NotFoundException;
use MailPoet\Segments\SegmentsRepository;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\ORMException;

class SegmentSaveController {
  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var FilterDataMapper */
  private $filterDataMapper;

  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    SegmentsRepository $segmentsRepository,
    FilterDataMapper $filterDataMapper,
    EntityManager $entityManager
  ) {
    $this->segmentsRepository = $segmentsRepository;
    $this->filterDataMapper = $filterDataMapper;
    $this->entityManager = $entityManager;
  }

  /**
   * @throws ConflictException
   * @throws NotFoundException
   * @throws Exceptions\InvalidFilterException
   * @throws ORMException
   */
  public function save(array $data = []): SegmentEntity {
    $id = isset($data['id']) ? (int)$data['id'] : null;
    $name = $data['name'] ?? '';

    if (!$this->segmentsRepository->isNameUnique($name, null) && isset($data['force_creation']) && $data['force_creation'] === 'true') {
      $name = $name . ' (' . wp_generate_password(5, false) . ')';
    }

    $description = $data['description'] ?? '';
    $filtersData = $this->filterDataMapper->map($data);

    return $this->segmentsRepository->createOrUpdate($name, $description, SegmentEntity::TYPE_DYNAMIC, $filtersData, $id);
  }

  public function duplicate(SegmentEntity $segmentEntity): SegmentEntity {
    $duplicate = clone $segmentEntity;
    // translators: %s is the name of the segment
    $duplicate->setName(sprintf(__('Copy of %s', 'mailpoet'), $segmentEntity->getName()));
    $this->segmentsRepository->verifyNameIsUnique($duplicate->getName(), $duplicate->getId());
    $this->entityManager->wrapInTransaction(function(EntityManager $entityManager) use ($duplicate, $segmentEntity) {
      foreach ($segmentEntity->getDynamicFilters() as $dynamicFilter) {
        $duplicateFilter = clone $dynamicFilter;
        $duplicate->addDynamicFilter($duplicateFilter);
        $duplicateFilter->setSegment($duplicate);
        $entityManager->persist($duplicateFilter);
      }
      $entityManager->persist($duplicate);
      $entityManager->flush();
    });

    return $duplicate;
  }
}
