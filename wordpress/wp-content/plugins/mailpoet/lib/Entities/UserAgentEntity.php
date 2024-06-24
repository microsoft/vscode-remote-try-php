<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_agents")
 */
class UserAgentEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;

  public const USER_AGENT_TYPE_HUMAN = 0;
  public const USER_AGENT_TYPE_MACHINE = 1;

  public const MACHINE_USER_AGENTS = [
    'Mozilla/5.0',
  ];

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $hash;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $userAgent;

  public function __construct(
    string $userAgent
  ) {
    $this->setUserAgent($userAgent);
  }

  public function getUserAgent(): string {
    return $this->userAgent;
  }

  public function setUserAgent(string $userAgent): void {
    $this->userAgent = $userAgent;
    $this->hash = (string)crc32($userAgent);
  }

  public function getHash(): string {
    return $this->hash;
  }

  public function getUserAgentType(): int {
    if (in_array($this->getUserAgent(), self::MACHINE_USER_AGENTS, true)) {
      return self::USER_AGENT_TYPE_MACHINE;
    }
    return self::USER_AGENT_TYPE_HUMAN;
  }
}
