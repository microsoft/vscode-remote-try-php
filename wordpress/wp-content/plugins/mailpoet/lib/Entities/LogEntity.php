<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use DateTimeInterface;
use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="log")
 */
class LogEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $name;

  /**
   * @ORM\Column(type="integer", nullable=true)
   * @var int|null
   */
  private $level;

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $message;

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $rawMessage;


  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $context;

  /**
   * @return string|null
   */
  public function getName(): ?string {
    return $this->name;
  }

  /**
   * @return int|null
   */
  public function getLevel(): ?int {
    return $this->level;
  }

  /**
   * @return string|null
   */
  public function getMessage(): ?string {
    return $this->message;
  }

  public function getRawMessage(): ?string {
    return $this->rawMessage;
  }

  public function getContext(): ?array {
    return (array)json_decode($this->context ?? '{}', true);
  }

  public function setName(?string $name): void {
    $this->name = $name;
  }

  public function setLevel(?int $level): void {
    $this->level = $level;
  }

  public function setMessage(?string $message): void {
    $this->message = $message;
  }

  public function setCreatedAt(DateTimeInterface $createdAt): void {
    $this->createdAt = $createdAt;
  }

  public function setRawMessage(string $message): void {
    $this->rawMessage = $message;
  }

  public function setContext(array $context): void {
    $str = json_encode($context);
    if ($str) {
      $this->context = $str;
    }
  }
}
