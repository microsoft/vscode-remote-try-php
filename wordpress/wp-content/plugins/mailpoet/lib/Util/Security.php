<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util;

if (!defined('ABSPATH')) exit;


use Exception;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Subscribers\SubscribersRepository;

class Security {
  const HASH_LENGTH = 12;
  const UNSUBSCRIBE_TOKEN_LENGTH = 15;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    NewslettersRepository $newslettersRepository,
    SubscribersRepository $subscribersRepository
  ) {
    $this->newslettersRepository = $newslettersRepository;
    $this->subscribersRepository = $subscribersRepository;
  }

  /**
   * Generate random lowercase alphanumeric string.
   * 1 lowercase alphanumeric character = 6 bits (because log2(36) = 5.17)
   * So 3 bytes = 4 characters
   * @param int $length Minimal lenght is 5
   * @return string
   */
  public static function generateRandomString($length = 5): string {
    $length = max(5, (int)$length);
    $string = base_convert(
      bin2hex(
        random_bytes( // phpcs:ignore
          (int)ceil(3 * $length / 4)
        )
      ),
      16,
      36
    );
    $result = substr($string, 0, $length);
    if (strlen($result) === $length) return $result;
    // in very rare occasions we generate a shorter string when random_bytes generates something starting with 0 let's try again
    return self::generateRandomString($length);
  }

  /**
   * @param int $length Maximal length is 32
   * @return string
   */
  public static function generateHash($length = null) {
    $length = ($length) ? $length : self::HASH_LENGTH;
    $authKey = self::generateRandomString(64);
    if (defined('AUTH_KEY')) {
      $authKey = AUTH_KEY;
    }
    return substr(
      hash_hmac('sha512', self::generateRandomString(64), $authKey),
      0,
      $length
    );
  }

  static public function generateUnsubscribeToken($model) {
    do {
      $token = self::generateRandomString(self::UNSUBSCRIBE_TOKEN_LENGTH);
      $found = $model::whereEqual('unsubscribe_token', $token)->count();
    } while ($found > 0);
    return $token;
  }

  public function generateUnsubscribeTokenByEntity($entity): string {
    $repository = null;
    if ($entity instanceof NewsletterEntity) {
      $repository = $this->newslettersRepository;
    } elseif ($entity instanceof SubscriberEntity) {
      $repository = $this->subscribersRepository;
    } else {
      throw new Exception('Unsupported Entity type');
    }

    do {
      $token = self::generateRandomString(self::UNSUBSCRIBE_TOKEN_LENGTH);
      $found = count($repository->findBy(['unsubscribeToken' => $token]));
    } while ($found > 0);
    return $token;
  }
}
