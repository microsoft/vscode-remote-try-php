<?php declare(strict_types = 1);

namespace MailPoet\Analytics;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\UnexpectedValueException;

class ReporterCampaignData {


  const STANDARD_7_DAYS = 'Number of standard newsletters sent in last 7 days';
  const STANDARD_30_DAYS = 'Number of standard newsletters sent in last 30 days';
  const STANDARD_3_MONTHS = 'Number of standard newsletters sent in last 3 months';
  const STANDARD_SEGMENT_7_DAYS = 'Number of standard newsletters sent to segment in last 7 days';
  const STANDARD_SEGMENT_30_DAYS = 'Number of standard newsletters sent to segment in last 30 days';
  const STANDARD_SEGMENT_3_MONTHS = 'Number of standard newsletters sent to segment in last 3 months';
  const STANDARD_FILTERED_SEGMENT_7_DAYS = 'Number of standard newsletters filtered by segment in last 7 days';
  const STANDARD_FILTERED_SEGMENT_30_DAYS = 'Number of standard newsletters filtered by segment in last 30 days';
  const STANDARD_FILTERED_SEGMENT_3_MONTHS = 'Number of standard newsletters filtered by segment in last 3 months';
  const AUTOMATION_7_DAYS = 'Number of automations campaigns sent in the last 7 days';
  const AUTOMATION_30_DAYS = 'Number of automations campaigns sent in the last 30 days';
  const AUTOMATION_3_MONTHS = 'Number of automations campaigns sent in the last 3 months';
  const RE_ENGAGEMENT_7_DAYS = 'Number of re-engagement campaigns sent in the last 7 days';
  const RE_ENGAGEMENT_30_DAYS = 'Number of re-engagement campaigns sent in the last 30 days';
  const RE_ENGAGEMENT_3_MONTHS = 'Number of re-engagement campaigns sent in the last 3 months';
  const RE_ENGAGEMENT_SEGMENT_7_DAYS = 'Number of re-engagement campaigns sent to segment in the last 7 days';
  const RE_ENGAGEMENT_SEGMENT_30_DAYS = 'Number of re-engagement campaigns sent to segment in the last 30 days';
  const RE_ENGAGEMENT_SEGMENT_3_MONTHS = 'Number of re-engagement campaigns sent to segment in the last 3 months';
  const RE_ENGAGEMENT_FILTERED_SEGMENT_7_DAYS = 'Number of re-engagement campaigns filtered by segment in the last 7 days';
  const RE_ENGAGEMENT_FILTERED_SEGMENT_30_DAYS = 'Number of re-engagement campaigns filtered by segment in the last 30 days';
  const RE_ENGAGEMENT_FILTERED_SEGMENT_3_MONTHS = 'Number of re-engagement campaigns filtered by segment in the last 3 months';
  const POST_NOTIFICATION_7_DAYS = 'Number of post notification campaigns sent in the last 7 days';
  const POST_NOTIFICATION_30_DAYS = 'Number of post notification campaigns sent in the last 30 days';
  const POST_NOTIFICATION_3_MONTHS = 'Number of post notification campaigns sent in the last 3 months';
  const POST_NOTIFICATION_SEGMENT_7_DAYS = 'Number of post notification campaigns sent to segment in the last 7 days';
  const POST_NOTIFICATION_SEGMENT_30_DAYS = 'Number of post notification campaigns sent to segment in the last 30 days';
  const POST_NOTIFICATION_SEGMENT_3_MONTHS = 'Number of post notification campaigns sent to segment in the last 3 months';
  const POST_NOTIFICATION_FILTERED_SEGMENT_7_DAYS = 'Number of post notification campaigns filtered by segment in the last 7 days';
  const POST_NOTIFICATION_FILTERED_SEGMENT_30_DAYS = 'Number of post notification campaigns filtered by segment in the last 30 days';
  const POST_NOTIFICATION_FILTERED_SEGMENT_3_MONTHS = 'Number of post notification campaigns filtered by segment in the last 3 months';

  const LEGACY_WELCOME_7_DAYS = 'Number of legacy welcome email campaigns sent in the last 7 days';
  const LEGACY_WELCOME_30_DAYS = 'Number of legacy welcome email campaigns sent in the last 30 days';
  const LEGACY_WELCOME_3_MONTHS = 'Number of legacy welcome email campaigns sent in the last 3 months';

  const LEGACY_ABANDONED_CART_7_DAYS = 'Number of legacy abandoned cart campaigns sent in the last 7 days';
  const LEGACY_ABANDONED_CART_30_DAYS = 'Number of legacy abandoned cart campaigns sent in the last 30 days';
  const LEGACY_ABANDONED_CART_3_MONTHS = 'Number of legacy abandoned cart campaigns sent in the last 3 months';

  const LEGACY_FIRST_PURCHASE_7_DAYS = 'Number of legacy first purchase campaigns sent in the last 7 days';
  const LEGACY_FIRST_PURCHASE_30_DAYS = 'Number of legacy first purchase campaigns sent in the last 30 days';
  const LEGACY_FIRST_PURCHASE_3_MONTHS = 'Number of legacy first purchase campaigns sent in the last 3 months';

  const LEGACY_PURCHASED_IN_CATEGORY_7_DAYS = 'Number of legacy purchased in category campaigns sent in the last 7 days';
  const LEGACY_PURCHASED_IN_CATEGORY_30_DAYS = 'Number of legacy purchased in category campaigns sent in the last 30 days';
  const LEGACY_PURCHASED_IN_CATEGORY_3_MONTHS = 'Number of legacy purchased in category campaigns sent in the last 3 months';

  const LEGACY_PURCHASED_PRODUCT_7_DAYS = 'Number of legacy purchased product campaigns sent in the last 7 days';
  const LEGACY_PURCHASED_PRODUCT_30_DAYS = 'Number of legacy purchased product campaigns sent in the last 30 days';
  const LEGACY_PURCHASED_PRODUCT_3_MONTHS = 'Number of legacy purchased product campaigns sent in the last 3 months';

  const TOTAL_CAMPAIGNS_7_DAYS = 'Number of campaigns sent in the last 7 days';
  const TOTAL_CAMPAIGNS_30_DAYS = 'Number of campaigns sent in the last 30 days';
  const TOTAL_CAMPAIGNS_3_MONTHS = 'Number of campaigns sent in the last 3 months';


  const TOTAL_CAMPAIGNS_SEGMENT_7_DAYS = 'Number of campaigns sent to segment in the last 7 days';
  const TOTAL_CAMPAIGNS_SEGMENT_30_DAYS = 'Number of campaigns sent to segment in the last 30 days';
  const TOTAL_CAMPAIGNS_SEGMENT_3_MONTHS = 'Number of campaigns sent to segment in the last 3 months';

  const TOTAL_CAMPAIGNS_FILTERED_SEGMENT_7_DAYS = 'Number of campaigns filtered by segment in the last 7 days';
  const TOTAL_CAMPAIGNS_FILTERED_SEGMENT_30_DAYS = 'Number of campaigns filtered by segment in the last 30 days';
  const TOTAL_CAMPAIGNS_FILTERED_SEGMENT_3_MONTHS = 'Number of campaigns filtered by segment in the last 3 months';

  /** @var SendingQueuesRepository */
  private $sendingQueuesRepository;

  public function __construct(
    SendingQueuesRepository $sendingQueuesRepository
  ) {
    $this->sendingQueuesRepository = $sendingQueuesRepository;
  }

  public function getCampaignAnalyticsProperties(): array {
    $returnData = [
      self::STANDARD_7_DAYS => 0,
      self::STANDARD_30_DAYS => 0,
      self::STANDARD_3_MONTHS => 0,

      self::STANDARD_SEGMENT_7_DAYS => 0,
      self::STANDARD_SEGMENT_30_DAYS => 0,
      self::STANDARD_SEGMENT_3_MONTHS => 0,

      self::STANDARD_FILTERED_SEGMENT_7_DAYS => 0,
      self::STANDARD_FILTERED_SEGMENT_30_DAYS => 0,
      self::STANDARD_FILTERED_SEGMENT_3_MONTHS => 0,

      self::AUTOMATION_7_DAYS => 0,
      self::AUTOMATION_30_DAYS => 0,
      self::AUTOMATION_3_MONTHS => 0,

      self::RE_ENGAGEMENT_7_DAYS => 0,
      self::RE_ENGAGEMENT_30_DAYS => 0,
      self::RE_ENGAGEMENT_3_MONTHS => 0,

      self::RE_ENGAGEMENT_SEGMENT_7_DAYS => 0,
      self::RE_ENGAGEMENT_SEGMENT_30_DAYS => 0,
      self::RE_ENGAGEMENT_SEGMENT_3_MONTHS => 0,

      self::RE_ENGAGEMENT_FILTERED_SEGMENT_7_DAYS => 0,
      self::RE_ENGAGEMENT_FILTERED_SEGMENT_30_DAYS => 0,
      self::RE_ENGAGEMENT_FILTERED_SEGMENT_3_MONTHS => 0,

      self::POST_NOTIFICATION_7_DAYS => 0,
      self::POST_NOTIFICATION_30_DAYS => 0,
      self::POST_NOTIFICATION_3_MONTHS => 0,

      self::POST_NOTIFICATION_SEGMENT_7_DAYS => 0,
      self::POST_NOTIFICATION_SEGMENT_30_DAYS => 0,
      self::POST_NOTIFICATION_SEGMENT_3_MONTHS => 0,

      self::POST_NOTIFICATION_FILTERED_SEGMENT_7_DAYS => 0,
      self::POST_NOTIFICATION_FILTERED_SEGMENT_30_DAYS => 0,
      self::POST_NOTIFICATION_FILTERED_SEGMENT_3_MONTHS => 0,

      // Legacy
      self::LEGACY_WELCOME_7_DAYS => 0,
      self::LEGACY_WELCOME_30_DAYS => 0,
      self::LEGACY_WELCOME_3_MONTHS => 0,

      self::LEGACY_ABANDONED_CART_7_DAYS => 0,
      self::LEGACY_ABANDONED_CART_30_DAYS => 0,
      self::LEGACY_ABANDONED_CART_3_MONTHS => 0,

      self::LEGACY_FIRST_PURCHASE_7_DAYS => 0,
      self::LEGACY_FIRST_PURCHASE_30_DAYS => 0,
      self::LEGACY_FIRST_PURCHASE_3_MONTHS => 0,

      self::LEGACY_PURCHASED_IN_CATEGORY_7_DAYS => 0,
      self::LEGACY_PURCHASED_IN_CATEGORY_30_DAYS => 0,
      self::LEGACY_PURCHASED_IN_CATEGORY_3_MONTHS => 0,

      self::LEGACY_PURCHASED_PRODUCT_7_DAYS => 0,
      self::LEGACY_PURCHASED_PRODUCT_30_DAYS => 0,
      self::LEGACY_PURCHASED_PRODUCT_3_MONTHS => 0,

      // Totals
      self::TOTAL_CAMPAIGNS_7_DAYS => 0,
      self::TOTAL_CAMPAIGNS_30_DAYS => 0,
      self::TOTAL_CAMPAIGNS_3_MONTHS => 0,
      self::TOTAL_CAMPAIGNS_SEGMENT_7_DAYS => 0,
      self::TOTAL_CAMPAIGNS_SEGMENT_30_DAYS => 0,
      self::TOTAL_CAMPAIGNS_SEGMENT_3_MONTHS => 0,
      self::TOTAL_CAMPAIGNS_FILTERED_SEGMENT_7_DAYS => 0,
      self::TOTAL_CAMPAIGNS_FILTERED_SEGMENT_30_DAYS => 0,
      self::TOTAL_CAMPAIGNS_FILTERED_SEGMENT_3_MONTHS => 0,
    ];

    $processedResults = $this->getProcessedCampaignAnalytics();

    foreach ($processedResults as $campaignId => $processedResult) {
      $isNewerThan7DaysAgo = $processedResult['sentLast7Days'] ?? false;
      $isNewerThan30DaysAgo = $processedResult['sentLast30Days'] ?? false;
      $isNewerThan3MonthsAgo = $processedResult['sentLast3Months'] ?? false;

      $newsletterType = $processedResult['newsletterType'];

      $wasSentToDynamicSegment = $processedResult['sentToSegment'] ?? false;
      $wasFilteredBySegment = $processedResult['filteredBySegment'] ?? false;

      // Totals
      if ($isNewerThan7DaysAgo) {
        $returnData[self::TOTAL_CAMPAIGNS_7_DAYS]++;
        $returnData[self::TOTAL_CAMPAIGNS_30_DAYS]++;
        $returnData[self::TOTAL_CAMPAIGNS_3_MONTHS]++;
        if ($wasSentToDynamicSegment) {
          $returnData[self::TOTAL_CAMPAIGNS_SEGMENT_7_DAYS]++;
          $returnData[self::TOTAL_CAMPAIGNS_SEGMENT_30_DAYS]++;
          $returnData[self::TOTAL_CAMPAIGNS_SEGMENT_3_MONTHS]++;
        }
        if ($wasFilteredBySegment) {
          $returnData[self::TOTAL_CAMPAIGNS_FILTERED_SEGMENT_7_DAYS]++;
          $returnData[self::TOTAL_CAMPAIGNS_FILTERED_SEGMENT_30_DAYS]++;
          $returnData[self::TOTAL_CAMPAIGNS_FILTERED_SEGMENT_3_MONTHS]++;
        }
      } elseif ($isNewerThan30DaysAgo) {
        $returnData[self::TOTAL_CAMPAIGNS_30_DAYS]++;
        $returnData[self::TOTAL_CAMPAIGNS_3_MONTHS]++;
        if ($wasSentToDynamicSegment) {
          $returnData[self::TOTAL_CAMPAIGNS_SEGMENT_30_DAYS]++;
          $returnData[self::TOTAL_CAMPAIGNS_SEGMENT_3_MONTHS]++;
        }
        if ($wasFilteredBySegment) {
          $returnData[self::TOTAL_CAMPAIGNS_FILTERED_SEGMENT_30_DAYS]++;
          $returnData[self::TOTAL_CAMPAIGNS_FILTERED_SEGMENT_3_MONTHS]++;
        }
      } elseif ($isNewerThan3MonthsAgo) {
        $returnData[self::TOTAL_CAMPAIGNS_3_MONTHS]++;
        if ($wasSentToDynamicSegment) {
          $returnData[self::TOTAL_CAMPAIGNS_SEGMENT_3_MONTHS]++;
        }
        if ($wasFilteredBySegment) {
          $returnData[self::TOTAL_CAMPAIGNS_FILTERED_SEGMENT_3_MONTHS]++;
        }
      }

      switch ($newsletterType) {
        case NewsletterEntity::TYPE_STANDARD:
          if ($isNewerThan7DaysAgo) {
            $returnData[self::STANDARD_7_DAYS]++;
            $returnData[self::STANDARD_30_DAYS]++;
            $returnData[self::STANDARD_3_MONTHS]++;
            if ($wasFilteredBySegment) {
              $returnData[self::STANDARD_FILTERED_SEGMENT_7_DAYS]++;
              $returnData[self::STANDARD_FILTERED_SEGMENT_30_DAYS]++;
              $returnData[self::STANDARD_FILTERED_SEGMENT_3_MONTHS]++;
            }
            if ($wasSentToDynamicSegment) {
              $returnData[self::STANDARD_SEGMENT_7_DAYS]++;
              $returnData[self::STANDARD_SEGMENT_30_DAYS]++;
              $returnData[self::STANDARD_SEGMENT_3_MONTHS]++;
            }
          } elseif ($isNewerThan30DaysAgo) {
            $returnData[self::STANDARD_30_DAYS]++;
            $returnData[self::STANDARD_3_MONTHS]++;
            if ($wasFilteredBySegment) {
              $returnData[self::STANDARD_FILTERED_SEGMENT_30_DAYS]++;
              $returnData[self::STANDARD_FILTERED_SEGMENT_3_MONTHS]++;
            }
            if ($wasSentToDynamicSegment) {
              $returnData[self::STANDARD_SEGMENT_30_DAYS]++;
              $returnData[self::STANDARD_SEGMENT_3_MONTHS]++;
            }
          } elseif ($isNewerThan3MonthsAgo) {
            $returnData[self::STANDARD_3_MONTHS]++;
            if ($wasFilteredBySegment) {
              $returnData[self::STANDARD_FILTERED_SEGMENT_3_MONTHS]++;
            }
            if ($wasSentToDynamicSegment) {
              $returnData[self::STANDARD_SEGMENT_3_MONTHS]++;
            }
          }
          break;
        case NewsletterEntity::TYPE_NOTIFICATION_HISTORY:
          if ($isNewerThan7DaysAgo) {
            $returnData[self::POST_NOTIFICATION_7_DAYS]++;
            $returnData[self::POST_NOTIFICATION_30_DAYS]++;
            $returnData[self::POST_NOTIFICATION_3_MONTHS]++;
            if ($wasSentToDynamicSegment) {
              $returnData[self::POST_NOTIFICATION_SEGMENT_7_DAYS]++;
              $returnData[self::POST_NOTIFICATION_SEGMENT_30_DAYS]++;
              $returnData[self::POST_NOTIFICATION_SEGMENT_3_MONTHS]++;
            }
            if ($wasFilteredBySegment) {
              $returnData[self::POST_NOTIFICATION_FILTERED_SEGMENT_7_DAYS]++;
              $returnData[self::POST_NOTIFICATION_FILTERED_SEGMENT_30_DAYS]++;
              $returnData[self::POST_NOTIFICATION_FILTERED_SEGMENT_3_MONTHS]++;
            }
          } elseif ($isNewerThan30DaysAgo) {
            $returnData[self::POST_NOTIFICATION_30_DAYS]++;
            $returnData[self::POST_NOTIFICATION_3_MONTHS]++;
            if ($wasSentToDynamicSegment) {
              $returnData[self::POST_NOTIFICATION_SEGMENT_30_DAYS]++;
              $returnData[self::POST_NOTIFICATION_SEGMENT_3_MONTHS]++;
            }
            if ($wasFilteredBySegment) {
              $returnData[self::POST_NOTIFICATION_FILTERED_SEGMENT_30_DAYS]++;
              $returnData[self::POST_NOTIFICATION_FILTERED_SEGMENT_3_MONTHS]++;
            }
          } elseif ($isNewerThan3MonthsAgo) {
            $returnData[self::POST_NOTIFICATION_3_MONTHS]++;
            if ($wasSentToDynamicSegment) {
              $returnData[self::POST_NOTIFICATION_SEGMENT_3_MONTHS]++;
            }
            if ($wasFilteredBySegment) {
              $returnData[self::POST_NOTIFICATION_FILTERED_SEGMENT_3_MONTHS]++;
            }
          }
          break;
        case NewsletterEntity::TYPE_RE_ENGAGEMENT:
          if ($isNewerThan7DaysAgo) {
            $returnData[self::RE_ENGAGEMENT_7_DAYS]++;
            $returnData[self::RE_ENGAGEMENT_30_DAYS]++;
            $returnData[self::RE_ENGAGEMENT_3_MONTHS]++;
            if ($wasSentToDynamicSegment) {
              $returnData[self::RE_ENGAGEMENT_SEGMENT_7_DAYS]++;
              $returnData[self::RE_ENGAGEMENT_SEGMENT_30_DAYS]++;
              $returnData[self::RE_ENGAGEMENT_SEGMENT_3_MONTHS]++;
            }
            if ($wasFilteredBySegment) {
              $returnData[self::RE_ENGAGEMENT_FILTERED_SEGMENT_7_DAYS]++;
              $returnData[self::RE_ENGAGEMENT_FILTERED_SEGMENT_30_DAYS]++;
              $returnData[self::RE_ENGAGEMENT_FILTERED_SEGMENT_3_MONTHS]++;
            }
          } elseif ($isNewerThan30DaysAgo) {
            $returnData[self::RE_ENGAGEMENT_30_DAYS]++;
            $returnData[self::RE_ENGAGEMENT_3_MONTHS]++;
            if ($wasSentToDynamicSegment) {
              $returnData[self::RE_ENGAGEMENT_SEGMENT_30_DAYS]++;
              $returnData[self::RE_ENGAGEMENT_SEGMENT_3_MONTHS]++;
            }
            if ($wasFilteredBySegment) {
              $returnData[self::RE_ENGAGEMENT_FILTERED_SEGMENT_30_DAYS]++;
              $returnData[self::RE_ENGAGEMENT_FILTERED_SEGMENT_3_MONTHS]++;
            }
          } elseif ($isNewerThan3MonthsAgo) {
            $returnData[self::RE_ENGAGEMENT_3_MONTHS]++;
            if ($wasSentToDynamicSegment) {
              $returnData[self::RE_ENGAGEMENT_SEGMENT_3_MONTHS]++;
            }
            if ($wasFilteredBySegment) {
              $returnData[self::RE_ENGAGEMENT_FILTERED_SEGMENT_3_MONTHS]++;
            }
          }
          break;
        case NewsletterEntity::TYPE_WELCOME:
          if ($isNewerThan7DaysAgo) {
            $returnData[self::LEGACY_WELCOME_7_DAYS]++;
            $returnData[self::LEGACY_WELCOME_30_DAYS]++;
            $returnData[self::LEGACY_WELCOME_3_MONTHS]++;
          } elseif ($isNewerThan30DaysAgo) {
            $returnData[self::LEGACY_WELCOME_30_DAYS]++;
            $returnData[self::LEGACY_WELCOME_3_MONTHS]++;
          } elseif ($isNewerThan3MonthsAgo) {
            $returnData[self::LEGACY_WELCOME_3_MONTHS]++;
          }
          break;
        case NewsletterEntity::TYPE_AUTOMATION:
          if ($isNewerThan7DaysAgo) {
            $returnData[self::AUTOMATION_7_DAYS]++;
            $returnData[self::AUTOMATION_30_DAYS]++;
            $returnData[self::AUTOMATION_3_MONTHS]++;
          } elseif ($isNewerThan30DaysAgo) {
            $returnData[self::AUTOMATION_30_DAYS]++;
            $returnData[self::AUTOMATION_3_MONTHS]++;
          } elseif ($isNewerThan3MonthsAgo) {
            $returnData[self::AUTOMATION_3_MONTHS]++;
          }
          break;
        // Legacy automatic emails.
        case 'purchasedProduct':
          if ($isNewerThan7DaysAgo) {
            $returnData[self::LEGACY_PURCHASED_PRODUCT_7_DAYS]++;
            $returnData[self::LEGACY_PURCHASED_PRODUCT_30_DAYS]++;
            $returnData[self::LEGACY_PURCHASED_PRODUCT_3_MONTHS]++;
          } elseif ($isNewerThan30DaysAgo) {
            $returnData[self::LEGACY_PURCHASED_PRODUCT_30_DAYS]++;
            $returnData[self::LEGACY_PURCHASED_PRODUCT_3_MONTHS]++;
          } elseif ($isNewerThan3MonthsAgo) {
            $returnData[self::LEGACY_PURCHASED_PRODUCT_3_MONTHS]++;
          }
          break;
        case 'purchasedInCategory':
          if ($isNewerThan7DaysAgo) {
            $returnData[self::LEGACY_PURCHASED_IN_CATEGORY_7_DAYS]++;
            $returnData[self::LEGACY_PURCHASED_IN_CATEGORY_30_DAYS]++;
            $returnData[self::LEGACY_PURCHASED_IN_CATEGORY_3_MONTHS]++;
          } elseif ($isNewerThan30DaysAgo) {
            $returnData[self::LEGACY_PURCHASED_IN_CATEGORY_30_DAYS]++;
            $returnData[self::LEGACY_PURCHASED_IN_CATEGORY_3_MONTHS]++;
          } elseif ($isNewerThan3MonthsAgo) {
            $returnData[self::LEGACY_PURCHASED_IN_CATEGORY_3_MONTHS]++;
          }
          break;
        case 'abandonedCart':
          if ($isNewerThan7DaysAgo) {
            $returnData[self::LEGACY_ABANDONED_CART_7_DAYS]++;
            $returnData[self::LEGACY_ABANDONED_CART_30_DAYS]++;
            $returnData[self::LEGACY_ABANDONED_CART_3_MONTHS]++;
          } elseif ($isNewerThan30DaysAgo) {
            $returnData[self::LEGACY_ABANDONED_CART_30_DAYS]++;
            $returnData[self::LEGACY_ABANDONED_CART_3_MONTHS]++;
          } elseif ($isNewerThan3MonthsAgo) {
            $returnData[self::LEGACY_ABANDONED_CART_3_MONTHS]++;
          }
          break;
        case 'firstPurchase':
          if ($isNewerThan7DaysAgo) {
            $returnData[self::LEGACY_FIRST_PURCHASE_7_DAYS]++;
            $returnData[self::LEGACY_FIRST_PURCHASE_30_DAYS]++;
            $returnData[self::LEGACY_FIRST_PURCHASE_3_MONTHS]++;
          } elseif ($isNewerThan30DaysAgo) {
            $returnData[self::LEGACY_FIRST_PURCHASE_30_DAYS]++;
            $returnData[self::LEGACY_FIRST_PURCHASE_3_MONTHS]++;
          } elseif ($isNewerThan3MonthsAgo) {
            $returnData[self::LEGACY_FIRST_PURCHASE_3_MONTHS]++;
          }
          break;
      }
    }

    return $returnData;
  }

  public function getProcessedCampaignAnalytics(): array {
    $rawData = $this->sendingQueuesRepository->getCampaignAnalyticsQuery()->getArrayResult();
    $processedResults = [];

    foreach ($rawData as $sendingInfo) {
      $meta = $sendingInfo['sendingQueueMeta'];
      $campaignId = $meta['campaignId'] ?? null;

      if (!is_string($campaignId)) {
        continue;
      }

      if (!isset($processedResults[$campaignId])) {
        $newsletterType = $sendingInfo['newsletterType'];
        $processedData = [
          'campaignId' => $campaignId,
          'newsletterType' => $newsletterType,
          'automaticSubType' => null,
          'sentToSegment' => (bool)$sendingInfo['sentToSegment'],
          'sentLast7Days' => (bool)$sendingInfo['sentLast7Days'],
          'sentLast30Days' => (bool)$sendingInfo['sentLast30Days'],
          'sentLast3Months' => (bool)$sendingInfo['sentLast3Months'],
          'filteredBySegment' => !!($meta['filterSegment'] ?? null),
        ];
        $processedResults[$campaignId] = $processedData;
        if ($newsletterType === NewsletterEntity::TYPE_AUTOMATIC) {
          try {
            // Although we could determine the subtype by joining the appropriate newsletter option field, using
            // the meta should be just as reliable, and we need the meta anyway, so this keeps our query simpler.
            $subType = $this->getLegacyAutomaticEmailSubtypeFromMeta($meta);
            $processedResults[$campaignId]['newsletterType'] = $subType;
          } catch (UnexpectedValueException $e) {
            // Ignore this error, the `automatic` email type won't be counted
          }
        }
      } else {
        if ($sendingInfo['sentLast7Days']) {
          $processedResults[$campaignId]['sentLast7Days'] = true;
        }
        if ($sendingInfo['sentLast30Days']) {
          $processedResults[$campaignId]['sentLast30Days'] = true;
        }
        if ($sendingInfo['sentLast3Months']) {
          $processedResults[$campaignId]['sentLast3Months'] = true;
        }
        if ($sendingInfo['sentToSegment']) {
          $processedResults[$campaignId]['sentToSegment'] = true;
        }
      }
    }

    return $processedResults;
  }

  private function getLegacyAutomaticEmailSubtypeFromMeta(array $meta): string {
    if (array_key_exists('orderedProducts', $meta)) {
      return 'purchasedProduct';
    }
    if (array_key_exists('orderedProductCategories', $meta)) {
      return 'purchasedInCategory';
    }
    if (array_key_exists('cart_product_ids', $meta)) {
      return 'abandonedCart';
    }
    if (array_key_exists('order_amount', $meta) && array_key_exists('order_date', $meta) && array_key_exists('order_id', $meta)) {
      return 'firstPurchase';
    }

    throw new UnexpectedValueException('Unknown automatic email type based on meta data');
  }
}
