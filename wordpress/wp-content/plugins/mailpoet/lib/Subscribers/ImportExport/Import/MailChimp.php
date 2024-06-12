<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\ImportExport\Import;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\Helpers;

class MailChimp {
  private const API_BASE_URI = 'https://user:%s@%s.api.mailchimp.com/3.0/';
  private const API_KEY_REGEX = '/[a-zA-Z0-9]{32}-[a-zA-Z0-9]{2,4}$/';
  private const API_BATCH_SIZE = 100;

  /** @var false|string  */
  public $apiKey;
  /** @var int */
  public $maxPostSize;
  /** @var false|string  */
  public $dataCenter;
  /** @var MailChimpDataMapper */
  private $mapper;

  public function __construct(
    string $apiKey
  ) {
    $this->apiKey = $this->getAPIKey($apiKey);
    $this->maxPostSize = (int)Helpers::getMaxPostSize('bytes');
    $this->dataCenter = $this->getDataCenter($this->apiKey);
    $this->mapper = new MailChimpDataMapper();
  }

  public function getLists(): array {
    if (!$this->apiKey || !$this->dataCenter) {
      $this->throwException('API');
    }

    $lists = [];
    $count = 0;
    while (true) {
      $data = $this->getApiData('lists', $count);
      if ($data === null) {
        $this->throwException('lists');
        break;
      }

      $count += count($data['lists']);
      foreach ($data['lists'] as $list) {
        $lists[] = [
          'id' => $list['id'],
          'name' => $list['name'],
        ];
      }

      if ($data['total_items'] <= $count) {
        break;
      }
    }

    return $lists;
  }

  public function getSubscribers(array $lists = []): array {
    if (!$this->apiKey || !$this->dataCenter) {
      $this->throwException('API');
    }

    if (!$lists) {
      $this->throwException('lists');
    }

    $subscribers = [];
    $duplicate = [];
    $disallowed = [];
    foreach ($lists as $list) {
      $count = 0;
      while (true) {
        $data = $this->getApiData("lists/{$list}/members", $count);
        if ($data === null) {
          $this->throwException('lists');
          break;
        }
        $count += count($data['members']);
        foreach ($data['members'] as $member) {
          $emailAddress = $member['email_address'];
          if (!$this->isSubscriberAllowed($member)) {
            $disallowed[$emailAddress] = $this->mapper->mapMember($member);
          } elseif (isset($subscribers[$emailAddress])) {
            $duplicate[$emailAddress] = $this->mapper->mapMember($member);
          } else {
            $subscribers[$emailAddress] = $this->mapper->mapMember($member);
          }
        }

        if ($data['total_items'] <= $count) {
          break;
        }
      }
    }

    if (!count($subscribers)) {
      $this->throwException('subscribers');
    }

    return [
      'subscribers' => array_values($subscribers),
      'invalid' => [],
      'duplicate' => $duplicate,
      'disallowed' => $disallowed,
      'role' => [],
      'header' => $this->mapper->getMembersHeader(),
      'subscribersCount' => count($subscribers),
    ];
  }

  /**
   * @param string|false $apiKey
   * @return false|string
   */
  public function getDataCenter($apiKey) {
    if (!$apiKey) return false;
    $apiKeyParts = explode('-', $apiKey);
    return end($apiKeyParts);
  }

  /**
   * @param string $apiKey
   * @return false|string
   */
  public function getAPIKey(string $apiKey) {
    return (preg_match(self::API_KEY_REGEX, $apiKey)) ? $apiKey : false;
  }

  /**
   * @param string $error
   * @throws \Exception
   */
  public function throwException(string $error): void {
    $errorMessage = __('Unknown MailChimp error.', 'mailpoet');
    switch ($error) {
      case 'API':
        $errorMessage = __('Invalid API Key.', 'mailpoet');
        break;
      case 'size':
        $errorMessage = __('The information received from MailChimp is too large for processing. Please limit the number of lists!', 'mailpoet');
        break;
      case 'subscribers':
        $errorMessage = __('Did not find any active subscribers.', 'mailpoet');
        break;
      case 'lists':
        $errorMessage = __('Did not find any valid lists.', 'mailpoet');
        break;
    }
    throw new \Exception($errorMessage);
  }

  public function isSubscriberAllowed(array $subscriber): bool {
    if (in_array($subscriber['status'], ['unsubscribed', 'cleaned', 'pending'], true)) {
      return false;
    }
    if ($subscriber['member_rating'] < 2) {
      return false;
    }
    // Rate 1 is on MailChimp API equal to 100% and we don't want to import avg_open_rate lower than 5%
    if ($subscriber['stats']['avg_open_rate'] < 0.05) {
      return false;
    }
    // We don't want to import avg_click_rate lower than 0.5%
    if ($subscriber['stats']['avg_click_rate'] < 0.005) {
      return false;
    }

    return true;
  }

  private function getApiData(string $endpoint, int $offset): ?array {
    $url = sprintf(self::API_BASE_URI, $this->apiKey, $this->dataCenter);
    $url .= $endpoint . '?' . http_build_query([
      'count' => self::API_BATCH_SIZE,
      'offset' => $offset,
    ]);

    $connection = @fopen($url, 'r');
    if (!$connection) {
      return null;
    }

    $bytesFetched = 0;
    $response = '';
    while (!feof($connection)) {
      $buffer = fgets($connection, 4096);
      if (!is_string($buffer)) {
        return null;
      }
      if (trim($buffer) !== '') {
        $response .= $buffer;
      }
      $bytesFetched += strlen((string)$buffer);
      if ($bytesFetched > $this->maxPostSize) {
        $this->throwException('size');
      }
    }
    fclose($connection);

    return json_decode($response, true);
  }
}
