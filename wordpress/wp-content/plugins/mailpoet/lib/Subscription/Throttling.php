<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscription;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberIPEntity;
use MailPoet\Subscribers\SubscriberIPsRepository;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;

class Throttling {
  /** @var SubscriberIPsRepository */
  private $subscriberIPsRepository;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    SubscriberIPsRepository $subscriberIPsRepository,
    WPFunctions $wp
  ) {
    $this->wp = $wp;
    $this->subscriberIPsRepository = $subscriberIPsRepository;
  }

  public function throttle() {
    $subscriptionLimitEnabled = $this->wp->applyFilters('mailpoet_subscription_limit_enabled', true);

    $subscriptionLimitWindow = (int)$this->wp->applyFilters('mailpoet_subscription_limit_window', DAY_IN_SECONDS);
    $subscriptionLimitBase = (int)$this->wp->applyFilters('mailpoet_subscription_limit_base', MINUTE_IN_SECONDS);

    $subscriberIp = Helpers::getIP();

    if ($subscriptionLimitEnabled && !$this->isUserExemptFromThrottling()) {
      if (!empty($subscriberIp)) {
        $subscriptionCount = $this->subscriberIPsRepository->getCountByIPAndCreatedAtAfterTimeInSeconds($subscriberIp, $subscriptionLimitWindow);
        if ($subscriptionCount > 0) {
          $timeout = $subscriptionLimitBase * pow(2, $subscriptionCount - 1);
          // Cap timeout and avoid float numbers
          $timeout = min($timeout, $subscriptionLimitWindow);
          $existingUser = $this->subscriberIPsRepository->findOneByIPAndCreatedAtAfterTimeInSeconds($subscriberIp, $timeout);
          if (!empty($existingUser)) {
            return $timeout;
          }
        }
      }
    }

    if ($subscriberIp !== null) {
      $ip = new SubscriberIPEntity($subscriberIp);
      $existingIp = $this->subscriberIPsRepository->findOneBy(['ip' => $ip->getIP(), 'createdAt' => $ip->getCreatedAt()]);
      if (!$existingIp) {
        $this->subscriberIPsRepository->persist($ip);
        $this->subscriberIPsRepository->flush();
      }
    }

    $this->purge();

    return false;
  }

  public function purge(): void {
    $interval = $this->wp->applyFilters('mailpoet_subscription_purge_window', MONTH_IN_SECONDS);
    $this->subscriberIPsRepository->deleteCreatedAtBeforeTimeInSeconds($interval);
  }

  public function secondsToTimeString($seconds): string {
    $hrs = floor($seconds / 3600);
    $min = floor($seconds % 3600 / 60);
    $sec = $seconds % 3600 % 60;
    $result = [
      // translators: %s is the number of hours
      'hours' => $hrs ? sprintf(__('%d hours', 'mailpoet'), $hrs) : '',
      // translators: %s is the number of minutes
      'minutes' => $min ? sprintf(__('%d minutes', 'mailpoet'), $min) : '',
      // translators: %s is the number of seconds
      'seconds' => $sec ? sprintf(__('%d seconds', 'mailpoet'), $sec) : '',
    ];
    return join(' ', array_filter($result));
  }

  private function isUserExemptFromThrottling(): bool {
    if (!$this->wp->isUserLoggedIn()) {
      return false;
    }
    $user = $this->wp->wpGetCurrentUser();
    $roles = $this->wp->applyFilters('mailpoet_subscription_throttling_exclude_roles', ['administrator', 'editor']);
    return !empty(array_intersect($roles, (array)$user->roles));
  }
}
