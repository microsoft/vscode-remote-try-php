<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\EntityTraits;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

trait AutoincrementedIdTrait {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue
   * @var int|null
   */
  private $id;

  /** @return int|null */
  public function getId() {
    return $this->id;
  }

  /** @param int|null $id */
  public function setId($id) {
    $this->id = $id;
  }
}
