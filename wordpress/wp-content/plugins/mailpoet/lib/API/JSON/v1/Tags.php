<?php declare(strict_types = 1);

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\Entities\TagEntity;
use MailPoet\Tags\TagRepository;

class Tags extends APIEndpoint {

  private $repository;

  public function __construct(
    TagRepository $repository
  ) {
    $this->repository = $repository;
  }

  public function create($data = []) {
    if (!isset($data['name'])) {
      return $this->badRequest([
        APIError::BAD_REQUEST => __('A tag needs to have a name.', 'mailpoet'),
      ]);
    }

    $data['name'] = sanitize_text_field(wp_unslash($data['name']));
    $data['description'] = isset($data['description']) ? sanitize_text_field(wp_unslash($data['description'])) : '';

    return $this->successResponse(
      $this->mapTagEntity($this->repository->createOrUpdate($data))
    );
  }

  public function listing() {
    return $this->successResponse(
      array_map(
        [$this, 'mapTagEntity'],
        $this->repository->findAll()
      )
    );
  }

  private function mapTagEntity(TagEntity $tag): array {
    return [
      'id' => $tag->getId(),
      'name' => $tag->getName(),
      'description' => $tag->getDescription(),
      'created_at' => $tag->getCreatedAt(),
      'updated_at' => $tag->getUpdatedAt(),
    ];
  }
}
