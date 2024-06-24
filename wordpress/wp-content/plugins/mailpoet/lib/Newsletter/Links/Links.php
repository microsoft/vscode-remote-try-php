<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Links;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository;
use MailPoet\DI\ContainerWrapper;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterLinkEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\InvalidStateException;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\Newsletter\Shortcodes\Categories\Link;
use MailPoet\Newsletter\Shortcodes\Shortcodes;
use MailPoet\Router\Endpoints\Track as TrackEndpoint;
use MailPoet\Router\Router;
use MailPoet\Subscribers\LinkTokens;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Util\Helpers;
use MailPoet\Util\pQuery\pQuery as DomParser;
use MailPoet\Util\Security;

class Links {
  const DATA_TAG_CLICK = '[mailpoet_click_data]';
  const DATA_TAG_OPEN = '[mailpoet_open_data]';
  const LINK_TYPE_SHORTCODE = 'shortcode';
  const LINK_TYPE_URL = 'link';

  /** @var LinkTokens */
  private $linkTokens;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var NewsletterLinkRepository */
  private $newsletterLinkRepository;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var SendingQueuesRepository */
  private $sendingQueueRepository;

  public function __construct(
    LinkTokens $linkTokens,
    SubscribersRepository $subscribersRepository,
    NewsletterLinkRepository $newsletterLinkRepository,
    NewslettersRepository $newslettersRepository,
    SendingQueuesRepository $sendingQueuesRepository
  ) {
    $this->linkTokens = $linkTokens;
    $this->subscribersRepository = $subscribersRepository;
    $this->newsletterLinkRepository = $newsletterLinkRepository;
    $this->newslettersRepository = $newslettersRepository;
    $this->sendingQueueRepository = $sendingQueuesRepository;
  }

  public function process($content, $newsletterId, $queueId) {
    $extractedLinks = $this->extract($content);
    $savedLinks = $this->load($newsletterId, $queueId);
    $processedLinks = $this->hash($extractedLinks, $savedLinks);
    return $this->replace($content, $processedLinks);
  }

  public function extract($content) {
    $extractedLinks = [];
    // extract link shortcodes
    /** @var Shortcodes $shortcodes */
    $shortcodes = ContainerWrapper::getInstance()->get(Shortcodes::class);
    $shortcodes = $shortcodes->extract(
      $content,
      $categories = [Link::CATEGORY_NAME]
    );
    if ($shortcodes) {
      $extractedLinks = array_map(function($shortcode) {
        return [
          'type' => Links::LINK_TYPE_SHORTCODE,
          'link' => $shortcode,
        ];
      }, $shortcodes);
    }
    // extract HTML anchor tags
    $DOM = DomParser::parseStr($content);
    foreach ($DOM->query('a') as $link) {
      if (!$link->href) continue;
      $extractedLinks[] = [
        'type' => self::LINK_TYPE_URL,
        'link' => $link->href,
      ];
    }
    return array_unique($extractedLinks, SORT_REGULAR);
  }

  public function replace($content, $processedLinks) {
    // replace HTML anchor tags
    $DOM = DomParser::parseStr($content);
    foreach ($DOM->query('a') as $link) {
      $linkToReplace = $link->href;
      $replacementLink = (!empty($processedLinks[$linkToReplace]['processed_link'])) ?
        $processedLinks[$linkToReplace]['processed_link'] :
        null;
      if (!$replacementLink) continue;
      $link->setAttribute('href', $replacementLink);
    }
    $content = $DOM->__toString();
    // replace link shortcodes and markdown links
    foreach ($processedLinks as $processedLink) {
      $linkToReplace = $processedLink['link'];
      $replacementLink = $processedLink['processed_link'];
      if ($processedLink['type'] == self::LINK_TYPE_SHORTCODE) {
        $content = str_replace($linkToReplace, $replacementLink, (string)$content);
      }
      $content = preg_replace(
        '/\[(.*?)\](\(' . preg_quote($linkToReplace, '/') . '\))/',
        '[$1](' . $replacementLink . ')',
        (string)$content
      );
    }
    return [
      $content,
      array_values($processedLinks),
    ];
  }

  public function replaceSubscriberData(
    $subscriberId,
    $queueId,
    $content,
    $preview = false
  ) {
    // match data tags
    $subscriber = $this->subscribersRepository->findOneById($subscriberId);
    if (!$subscriber) {
      throw new InvalidStateException();
    }
    preg_match_all($this->getLinkRegex(), $content, $matches);
    foreach ($matches[1] as $index => $match) {
      $hash = null;
      if (preg_match('/-/', $match)) {
        [, $hash] = explode('-', $match);
      }
      $data = $this->createUrlDataObject(
        $subscriber->getId(),
        $this->linkTokens->getToken($subscriber),
        $queueId,
        $hash,
        $preview
      );
      $routerAction = ($matches[2][$index] === self::DATA_TAG_CLICK) ?
        TrackEndpoint::ACTION_CLICK :
        TrackEndpoint::ACTION_OPEN;
      $link = Router::buildRequest(
        TrackEndpoint::ENDPOINT,
        $routerAction,
        $data
      );
      $content = str_replace($match, $link, $content);
    }
    return $content;
  }

  public function save(array $links, $newsletterId, $queueId) {
    foreach ($links as $link) {
      if (isset($link['id'])) {
        continue;
      }

      if (empty($link['hash']) || empty($link['link'])) {
        continue;
      }

      $newsletter = $this->newslettersRepository->getReference($newsletterId);
      $sendingQueue = $this->sendingQueueRepository->getReference($queueId);

      if (!$newsletter instanceof NewsletterEntity || !$sendingQueue instanceof SendingQueueEntity) {
        continue;
      }

      $newsletterLink = new NewsletterLinkEntity($newsletter, $sendingQueue, $link['link'], $link['hash']);
      $this->newsletterLinkRepository->persist($newsletterLink);
    }

    $this->newsletterLinkRepository->flush();
  }

  public function ensureInstantUnsubscribeLink(array $processedLinks) {
    if (
      in_array(
        NewsletterLinkEntity::INSTANT_UNSUBSCRIBE_LINK_SHORT_CODE,
        array_column($processedLinks, 'link')
      )
    ) {
      return $processedLinks;
    }
    $processedLinks[] = $this->hashLink(
      NewsletterLinkEntity::INSTANT_UNSUBSCRIBE_LINK_SHORT_CODE,
      Links::LINK_TYPE_SHORTCODE
    );
    return $processedLinks;
  }

  public function convertHashedLinksToShortcodesAndUrls($content, $queueId, $convertAll = false) {
    preg_match_all($this->getLinkRegex(), $content, $links);
    $links = array_unique(Helpers::flattenArray($links));
    foreach ($links as $link) {
      $linkHash = explode('-', $link);

      if (!isset($linkHash[1])) {
        continue;
      }

      $newsletterLink = $this->newsletterLinkRepository->findOneBy(['hash' => $linkHash[1], 'queue' => $queueId]);

      // convert either only link shortcodes or all hashes links if "convert all"
      // option is specified
      if (
        ($newsletterLink instanceof NewsletterLinkEntity) &&
        (preg_match('/\[link:/', $newsletterLink->getUrl()) || $convertAll)
      ) {
        $content = str_replace($link, $newsletterLink->getUrl(), $content);
      }
    }
    return $content;
  }

  public function getLinkRegex() {
    return sprintf(
      '/((%s|%s)(?:-\w+)?)/',
      preg_quote(self::DATA_TAG_CLICK),
      preg_quote(self::DATA_TAG_OPEN)
    );
  }

  public function createUrlDataObject(
    $subscriberId, $subscriberLinkToken, $queueId, $linkHash, $preview
  ) {
    return [
      (string)$subscriberId,
      $subscriberLinkToken,
      (string)$queueId,
      $linkHash,
      $preview,
    ];
  }

  public function transformUrlDataObject($data) {
    reset($data);
    if (!is_int(key($data))) return $data;
    $transformedData = [];
    $transformedData['subscriber_id'] = (!empty($data[0])) ? $data[0] : false;
    $transformedData['subscriber_token'] = (!empty($data[1])) ? $data[1] : false;
    $transformedData['queue_id'] = (!empty($data[2])) ? $data[2] : false;
    $transformedData['link_hash'] = (!empty($data[3])) ? $data[3] : false;
    $transformedData['preview'] = (!empty($data[4])) ? $data[4] : false;
    return $transformedData;
  }

  private static function hashLink($link, $type) {
    $hash = Security::generateHash();
    return [
      'type' => $type,
      'hash' => $hash,
      'link' => $link,
      // replace link with a temporary data tag + hash
      // it will be further replaced with the proper track API URL during sending
      'processed_link' => self::DATA_TAG_CLICK . '-' . $hash,
    ];
  }

  private function hash($extractedLinks, $savedLinks) {
    $processedLinks = array_map(function($link) {
      $link['type'] = Links::LINK_TYPE_URL;
      $link['link'] = $link['url'];
      $link['processed_link'] = self::DATA_TAG_CLICK . '-' . $link['hash'];
      return $link;
    }, $savedLinks);
    foreach ($extractedLinks as $extractedLink) {
      $link = $extractedLink['link'];
      if (array_key_exists($link, $processedLinks))
        continue;
      // Use URL as a key to map between extracted and processed links
      // regardless of their sequential position (useful for link skips etc.)
      $processedLinks[$link] = $this->hashLink($link, $extractedLink['type']);
    }
    return $processedLinks;
  }

  private function load($newsletterId, $queueId) {
    $links = $this->newsletterLinkRepository->findBy(
      ['newsletter' => $newsletterId, 'queue' => $queueId]
    );

    $savedLinks = [];
    foreach ($links as $link) {
      $savedLinks[$link->getUrl()] = $link->toArray();
    }
    return $savedLinks;
  }
}
