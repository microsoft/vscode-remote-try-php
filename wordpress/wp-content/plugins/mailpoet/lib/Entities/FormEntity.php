<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\DeletedAtTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="forms")
 */
class FormEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use DeletedAtTrait;

  const DISPLAY_TYPE_BELOW_POST = 'below_posts';
  const DISPLAY_TYPE_FIXED_BAR = 'fixed_bar';
  const DISPLAY_TYPE_POPUP = 'popup';
  const DISPLAY_TYPE_SLIDE_IN = 'slide_in';
  const DISPLAY_TYPE_OTHERS = 'others';

  const STATUS_ENABLED = 'enabled';
  const STATUS_DISABLED = 'disabled';

  const HTML_BLOCK_TYPE = 'html';
  const HEADING_BLOCK_TYPE = 'heading';
  const IMAGE_BLOCK_TYPE = 'image';
  const PARAGRAPH_BLOCK_TYPE = 'paragraph';
  const DIVIDER_BLOCK_TYPE = 'divider';
  const CHECKBOX_BLOCK_TYPE = 'checkbox';
  const RADIO_BLOCK_TYPE = 'radio';
  const SEGMENT_SELECTION_BLOCK_TYPE = 'segment';
  const DATE_BLOCK_TYPE = 'date';
  const SELECT_BLOCK_TYPE = 'select';
  const TEXT_BLOCK_TYPE = 'text';
  const TEXTAREA_BLOCK_TYPE = 'textarea';
  const SUBMIT_BLOCK_TYPE = 'submit';
  const COLUMNS_BLOCK_TYPE = 'columns';
  const COLUMN_BLOCK_TYPE = 'column';

  public const FORM_FIELD_TYPES = [
    self::CHECKBOX_BLOCK_TYPE,
    self::RADIO_BLOCK_TYPE,
    self::SEGMENT_SELECTION_BLOCK_TYPE,
    self::DATE_BLOCK_TYPE,
    self::SELECT_BLOCK_TYPE,
    self::TEXT_BLOCK_TYPE,
    self::TEXTAREA_BLOCK_TYPE,
  ];

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $name;

  /**
   * @ORM\Column(type="serialized_array")
   * @var array|null
   */
  private $body;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $status;

  /**
   * @ORM\Column(type="serialized_array")
   * @var array|null
   */
  private $settings;

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $styles;

  public function __construct(
    $name
  ) {
    $this->name = $name;
    $this->status = self::STATUS_ENABLED;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return array|null
   */
  public function getBody() {
    return $this->body;
  }

  /**
   * @return array|null
   */
  public function getSettings() {
    return $this->settings;
  }

  /**
   * @return string|null
   */
  public function getStyles() {
    return $this->styles;
  }

  /**
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @param array|null $body
   */
  public function setBody($body) {
    $this->body = $body;
  }

  /**
   * @param array|null $settings
   */
  public function setSettings($settings) {
    $this->settings = $settings;
  }

  /**
   * @param string|null $styles
   */
  public function setStyles($styles) {
    $this->styles = $styles;
  }

  /**
   * @param string $status
   */
  public function setStatus(string $status) {
    $this->status = $status;
  }

  /**
   * @return string
   */
  public function getStatus(): string {
    return $this->status;
  }

  public function toArray(): array {
    return [
      'id' => $this->getId(),
      'name' => $this->getName(),
      'body' => $this->getBody(),
      'settings' => $this->getSettings(),
      'styles' => $this->getStyles(),
      'status' => $this->getStatus(),
      'created_at' => $this->getCreatedAt(),
      'updated_at' => $this->getUpdatedAt(),
      'deleted_at' => $this->getDeletedAt(),
    ];
  }

  public function getBlocksByTypes(array $types, array $blocks = null): array {
    $found = [];
    if ($blocks === null) {
      $blocks = $this->getBody() ?? [];
    }
    foreach ($blocks as $block) {
      if (isset($block['type']) && in_array($block['type'], $types, true)) {
        $found[] = $block;
      }
      if (isset($block['body']) && is_array($block['body']) && !empty($block['body'])) {
        $found = array_merge($found, $this->getBlocksByTypes($types, $block['body']));
      }
    }
    return $found;
  }

  public function getSegmentBlocksSegmentIds(): array {
    $listSelectionBlocks = $this->getBlocksByTypes([FormEntity::SEGMENT_SELECTION_BLOCK_TYPE]);
    $listSelection = [];
    foreach ($listSelectionBlocks as $listSelectionBlock) {
      $listSelection = array_unique(
        array_merge(
          $listSelection,
          array_column($listSelectionBlock['params']['values'] ?? [], 'id')
        )
      );
    }
    return $listSelection;
  }

  public function getSettingsSegmentIds(): array {
    return $this->settings['segments'] ?? [];
  }
}
