<?php declare(strict_types = 1);

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\RuntimeException;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="posts")
 */
class WpPostEntity {
  /**
   * @ORM\Column(type="integer", name="ID")
   * @ORM\Id
   * @ORM\GeneratedValue
   * @var int
   */
  private $id;

  /**
   * @ORM\Column(type="string", name="post_title")
   * @var string
   */
  private $postTitle;

  public function __construct() {
    throw new RuntimeException('WpPostEntity is read only and cannot be instantiated.');
  }

  public function getId(): int {
    return $this->id;
  }

  public function getPostTitle(): string {
    return $this->postTitle;
  }

  /**
   * We don't use typehint for now because doctrine cache generator would fail as it doesn't know the class.
   * @return \WP_Post|null
   */
  public function getWpPostInstance() {
    $post = \WP_Post::get_instance($this->id);
    return $post ?: null;
  }
}
