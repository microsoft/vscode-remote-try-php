<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Statistics;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Newsletter\Links\Links as NewsletterLinks;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Util\Helpers;
use MailPoet\Util\SecondLevelDomainNames;
use MailPoet\WP\Functions;

class GATracking {

  /** @var SecondLevelDomainNames */
  private $secondLevelDomainNames;

  /** @var NewsletterLinks */
  private $newsletterLinks;

  /** @var Functions */
  private $wp;

  /** @var TrackingConfig */
  private $tackingConfig;

  public function __construct(
    NewsletterLinks $newsletterLinks,
    Functions $wp,
    TrackingConfig $trackingConfig
  ) {
    $this->secondLevelDomainNames = new SecondLevelDomainNames();
    $this->newsletterLinks = $newsletterLinks;
    $this->wp = $wp;
    $this->tackingConfig = $trackingConfig;
  }

  public function applyGATracking($renderedNewsletter, NewsletterEntity $newsletter, $internalHost = null) {
    if (!$this->tackingConfig->isEmailTrackingEnabled()) {
      return $renderedNewsletter;
    }
    if ($newsletter->getType() == NewsletterEntity::TYPE_NOTIFICATION_HISTORY && $newsletter->getParent() instanceof NewsletterEntity) {
      $parentNewsletter = $newsletter->getParent();
      $field = $parentNewsletter->getGaCampaign();
    } else {
      $field = $newsletter->getGaCampaign();
    }

    return $this->addGAParamsToLinks($renderedNewsletter, $field, $internalHost);
  }

  private function addGAParamsToLinks($renderedNewsletter, $gaCampaign, $internalHost = null) {
    // join HTML and TEXT rendered body into a text string
    $content = Helpers::joinObject($renderedNewsletter);
    $extractedLinks = $this->newsletterLinks->extract($content);
    $processedLinks = $this->addParams($extractedLinks, $gaCampaign, $internalHost);
    list($content, $links) = $this->newsletterLinks->replace($content, $processedLinks);
    // split the processed body with hashed links back to HTML and TEXT
    list($renderedNewsletter['html'], $renderedNewsletter['text'])
      = Helpers::splitObject($content);
    return $renderedNewsletter;
  }

  private function addParams($extractedLinks, $gaCampaign, $internalHost = null) {
    $processedLinks = [];
    $params = [
      'utm_source' => 'mailpoet',
      'utm_medium' => 'email',
      'utm_source_platform' => 'mailpoet',
    ];
    if ($gaCampaign) {
      $params['utm_campaign'] = $gaCampaign;
    }
    $internalHost = $internalHost ?: parse_url(home_url(), PHP_URL_HOST);
    $internalHost = $this->secondLevelDomainNames->get($internalHost);
    foreach ($extractedLinks as $extractedLink) {
      if ($extractedLink['type'] !== NewsletterLinks::LINK_TYPE_URL) {
        continue;
      } elseif (strpos((string)parse_url($extractedLink['link'], PHP_URL_HOST), $internalHost) === false) {
        // Process only internal links (i.e. pointing to current site)
        continue;
      }

      $link = $extractedLink['link'];

      // Do not overwrite existing query parameters
      $parsedUrl = parse_url($link);
      $linkParams = $params;
      if (isset($parsedUrl['query'])) {
        foreach (array_keys($params) as $param) {
          if (strpos($parsedUrl['query'], $param . '=') !== false) {
            unset($linkParams[$param]);
          }
        }
      }

      $processedLink = $this->wp->applyFilters(
        'mailpoet_ga_tracking_link',
        $this->wp->addQueryArg($linkParams, $link),
        $extractedLink['link'],
        $linkParams,
        $extractedLink['type']
      );
      $processedLinks[$link] = [
        'type' => $extractedLink['type'],
        'link' => $link,
        'processed_link' => $processedLink,
      ];
    }
    return $processedLinks;
  }
}
