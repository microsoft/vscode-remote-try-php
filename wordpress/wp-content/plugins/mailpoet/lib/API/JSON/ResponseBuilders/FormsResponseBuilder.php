<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\ResponseBuilders;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\FormEntity;
use MailPoet\Statistics\StatisticsFormsRepository;

class FormsResponseBuilder {
  const DATE_FORMAT = 'Y-m-d H:i:s';

  /** @var StatisticsFormsRepository */
  private $statisticsFormsRepository;

  public function __construct(
    StatisticsFormsRepository $statisticsFormsRepository
  ) {
    $this->statisticsFormsRepository = $statisticsFormsRepository;
  }

  public function build(FormEntity $form) {
    return [
      'id' => (string)$form->getId(), // (string) for BC
      'name' => $form->getName(),
      'status' => $form->getStatus(),
      'body' => $form->getBody(),
      'settings' => $form->getSettings(),
      'styles' => $form->getStyles(),
      'created_at' => ($createdAt = $form->getCreatedAt()) ? $createdAt->format(self::DATE_FORMAT) : null,
      'updated_at' => $form->getUpdatedAt()->format(self::DATE_FORMAT),
      'deleted_at' => ($deletedAt = $form->getDeletedAt()) ? $deletedAt->format(self::DATE_FORMAT) : null,
    ];
  }

  public function buildForListing(array $forms) {
    $data = [];

    foreach ($forms as $form) {
      $form = $this->build($form);
      $form['signups'] = $this->statisticsFormsRepository->getTotalSignups($form['id']);
      $form['segments'] = (
        !empty($form['settings']['segments'])
        ? $form['settings']['segments']
        : []
      );

      $data[] = $form;
    }

    return $data;
  }
}
