<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Mailer;

if (!defined('ABSPATH')) exit;


use MailPoet\Logging\LoggerFactory;
use MailPoet\Settings\SettingsController;

/**
 * @phpstan-type MailerLogError array{
 *    "error_code"?: non-empty-string,
 *    "error_message": string,
 *    "operation": string
 *   }
 * @phpstan-type MailerLogData array{
 *   "sent": array<string,int>,
 *   "started": int,
 *   "status": ?string,
 *   "retry_attempt": ?int,
 *   "retry_at": ?int,
 *   "error": ?MailerLogError,
 *   "transactional_email_last_error_at": ?int,
 *   "transactional_email_error_count": ?int,
 * }
 */

class MailerLog {
  const SETTING_NAME = 'mta_log';
  const STATUS_PAUSED = 'paused';
  const RETRY_ATTEMPTS_LIMIT = 3;
  const RETRY_INTERVAL = 120; // seconds

  /**
   * @param MailerLogData|null $mailerLog
   * @return MailerLogData
   */
  public static function getMailerLog(array $mailerLog = null): array {
    if ($mailerLog) return $mailerLog;
    $settings = SettingsController::getInstance();
    $mailerLog = $settings->get(self::SETTING_NAME);
    if (!$mailerLog) {
      $mailerLog = self::createMailerLog();
    }
    /**
     * The old "sent" entry was just the number of emails.
     * We need to update this entry to the new data structure.
     */
    $mailerLog['sent'] = is_numeric($mailerLog['sent']) ? [self::sentEntriesDate(time() - 1) => $mailerLog['sent']] : (array)$mailerLog['sent'];
    return $mailerLog;
  }

  /**
   * @return MailerLogData
   */
  public static function createMailerLog(): array {
    $mailerLog = [
      'sent' => [],
      'started' => time(),
      'status' => null,
      'retry_attempt' => null,
      'retry_at' => null,
      'error' => null,
      'transactional_email_last_error_at' => null,
      'transactional_email_error_count' => null,
    ];
    $settings = SettingsController::getInstance();
    $settings->set(self::SETTING_NAME, $mailerLog);
    return $mailerLog;
  }

  /**
   * @return MailerLogData
   */
  public static function resetMailerLog(): array {
    return self::createMailerLog();
  }

  /**
   * @param MailerLogData $mailerLog
   * @return MailerLogData
   */
  public static function updateMailerLog(array $mailerLog): array {
    $mailerLog = self::removeOutdatedSentInformationFromMailerlog($mailerLog);
    $settings = SettingsController::getInstance();
    $settings->set(self::SETTING_NAME, $mailerLog);
    return $mailerLog;
  }

  /**
   * @param MailerLogData|null $mailerLog
   * @return null
   * @throws \Exception
   */
  public static function enforceExecutionRequirements(array $mailerLog = null) {
    $mailerLog = self::getMailerLog($mailerLog);
    if ($mailerLog['retry_attempt'] === self::RETRY_ATTEMPTS_LIMIT) {
      $mailerLog = self::pauseSending($mailerLog);
    }
    if (self::isSendingPaused($mailerLog)) {
      throw new \Exception(__('Sending has been paused.', 'mailpoet'));
    }
    if (self::isSendingWaitingForRetry($mailerLog)) {
      throw new \Exception(__('Sending is waiting to be retried.', 'mailpoet'));
    } else {
      $mailerLog['retry_at'] = null;
      self::updateMailerLog($mailerLog);
    }

    // ensure that sending frequency has not been reached
    if (self::isSendingLimitReached($mailerLog)) {
      throw new \Exception(__('Sending frequency limit has been reached.', 'mailpoet'));
    }
    return null;
  }

  /**
   * @param MailerLogData $mailerLog
   * @return MailerLogData
   */
  public static function pauseSending($mailerLog): array {
    $mailerLog['status'] = self::STATUS_PAUSED;
    $mailerLog['retry_attempt'] = null;
    $mailerLog['retry_at'] = null;
    $mailerLog['transactional_email_last_error_at'] = null;
    $mailerLog['transactional_email_error_count'] = null;
    return self::updateMailerLog($mailerLog);
  }

  /**
   * @return MailerLogData
   */
  public static function resumeSending(): array {
    return self::resetMailerLog();
  }

  /**
   * Process error, doesn't increase retry_attempt so it will not block sending
   *
   * @param string $operation
   * @param string $errorMessage
   * @param int $retryInterval
   *
   * @throws \Exception
   */
  public static function processNonBlockingError(string $operation, string $errorMessage, int $retryInterval = self::RETRY_INTERVAL) {
    $mailerLog = self::getMailerLog();
    $mailerLog['retry_at'] = time() + $retryInterval;
    $mailerLog = self::setError($mailerLog, $operation, $errorMessage);
    self::updateMailerLog($mailerLog);
    self::enforceExecutionRequirements();
  }

  /**
   * Process error, increase retry_attempt and block sending if it goes above RETRY_INTERVAL
   *
   * @param string $operation
   * @param string $errorMessage
   * @param string $errorCode
   * @param bool $pauseSending
   *
   * @throws \Exception
   */
  public static function processError(
    string $operation,
    string $errorMessage,
    string $errorCode = null,
    bool $pauseSending = false,
    int $throttledBatchSize = null
  ) {
    $mailerLog = self::getMailerLog();
    if (!isset($throttledBatchSize) || $throttledBatchSize === 1) {
      $mailerLog['retry_attempt']++;
    }
    $mailerLog['retry_at'] = time() + self::RETRY_INTERVAL;
    $mailerLog = self::setError($mailerLog, $operation, $errorMessage, $errorCode);
    self::updateMailerLog($mailerLog);
    if ($pauseSending) {
      LoggerFactory::getInstance()->getLogger(LoggerFactory::TOPIC_SENDING)->error(
        'Email sending was paused due an error',
        [
          'error_message' => $errorMessage,
          'error_code' => $errorCode,
        ]
      );
      self::pauseSending($mailerLog);
    }
    self::enforceExecutionRequirements();
  }

  /**
   * Process error, increase transactional_email_error_count and pauses sending if it reaches retry limit
   * This method is meant to be used for processing errors when sending transactional emails
   * like: Confirmation Email, Preview email, Stats Notification etc.
   *
   * @throws \Exception
   */
  public static function processTransactionalEmailError(
    string $operation,
    string $errorMessage,
    ?string $errorCode = null
  ): void {
    $mailerLog = self::getMailerLog();
    $lastErrorTime = $mailerLog['transactional_email_last_error_at'] ?? null;
    $ignoreErrorThreshold = time() - (2 * 60); // 2 minutes ago
    // We want to log the error max one time per 2 minutes
    if ($lastErrorTime && $lastErrorTime > $ignoreErrorThreshold) {
      return;
    }
    $mailerLog = self::setError($mailerLog, $operation, $errorMessage, $errorCode);
    $mailerLog['transactional_email_last_error_at'] = time();
    $mailerLog['transactional_email_error_count'] = ($mailerLog['transactional_email_error_count'] ?? 0) + 1;
    self::updateMailerLog($mailerLog);
    if ($mailerLog['transactional_email_error_count'] >= self::RETRY_ATTEMPTS_LIMIT) {
      LoggerFactory::getInstance()->getLogger(LoggerFactory::TOPIC_SENDING)->error(
        'Email sending was paused due a transactional email error',
        [
          'error_message' => $errorMessage,
          'error_code' => $errorCode,
        ]
      );
      self::pauseSending($mailerLog);
    }
  }

  /**
   * @param MailerLogData $mailerLog
   * @param string $operation
   * @param string $errorMessage
   * @param string|null $errorCode
   * @return MailerLogData
   */
  public static function setError(
    array $mailerLog,
    string $operation,
    string $errorMessage,
    string $errorCode = null
  ): array {
    $mailerLog['error'] = [
      'operation' => $operation,
      'error_message' => $errorMessage,
    ];
    if ($errorCode) {
      $mailerLog['error']['error_code'] = $errorCode;
    }
    return $mailerLog;
  }

  /**
   * @param MailerLogData|null $mailerLog
   * @return MailerLogError|null
   */
  public static function getError(array $mailerLog = null): ?array {
    $mailerLog = self::getMailerLog($mailerLog);
    return isset($mailerLog['error']) ? $mailerLog['error'] : null;
  }

  /**
   * @return MailerLogData|null
   */
  public static function incrementSentCount(): ?array {
    $settings = SettingsController::getInstance();
    $mailerConfig = $settings->get(Mailer::MAILER_CONFIG_SETTING_NAME);
    $mailerLog = self::getMailerLog();

    // do not increment count if sending limit is reached
    if (self::isSendingLimitReached($mailerLog)) {
      return null;
    }
    // clear previous retry count, errors, etc.
    if ($mailerLog['error'] !== null) {
      $mailerLog = self::clearSendingErrorLog($mailerLog);
    }

    // do not enforce sending limit for MailPoet's sending method
    if ($mailerConfig['method'] === Mailer::METHOD_MAILPOET) {
      return null;
    }

    $time = self::sentEntriesDate();
    if (!isset($mailerLog['sent'][$time])) {
      $mailerLog['sent'][$time] = 0;
    }
    $mailerLog['sent'][$time]++;
    return self::updateMailerLog($mailerLog);
  }

  /**
   * @param MailerLogData $mailerLog
   * @return MailerLogData
   */
  public static function clearSendingErrorLog(array $mailerLog): array {
    $mailerLog['retry_attempt'] = null;
    $mailerLog['retry_at'] = null;
    $mailerLog['error'] = null;
    $mailerLog['transactional_email_last_error_at'] = null;
    $mailerLog['transactional_email_error_count'] = null;
    return self::updateMailerLog($mailerLog);
  }

  /**
   * @param MailerLogData|null $mailerLog
   * @return bool
   */
  public static function isSendingLimitReached(array $mailerLog = null): bool {
    $settings = SettingsController::getInstance();
    $mailerConfig = $settings->get(Mailer::MAILER_CONFIG_SETTING_NAME);
    // do not enforce sending limit for MailPoet's sending method
    if ($mailerConfig['method'] === Mailer::METHOD_MAILPOET) return false;
    $mailerLog = self::getMailerLog($mailerLog);

    if (empty($mailerConfig['frequency'])) {
      $defaultSettings = $settings->getAllDefaults();
      $mailerConfig['frequency'] = $defaultSettings['mta']['frequency'];
    }
    $frequencyInterval = (int)$mailerConfig['frequency']['interval'] * Mailer::SENDING_LIMIT_INTERVAL_MULTIPLIER;
    $frequencyLimit = (int)$mailerConfig['frequency']['emails'];
    $sent = self::sentSince($frequencyInterval, $mailerLog);
    return $sent >= $frequencyLimit;
  }

  /**
   * @param int|null $sinceSeconds
   * @param MailerLogData|null $mailerLog
   * @return int
   */
  public static function sentSince(int $sinceSeconds = null, array $mailerLog = null): int {

    if ($sinceSeconds === null) {
      $settings = SettingsController::getInstance();
      $mailerConfig = $settings->get(Mailer::MAILER_CONFIG_SETTING_NAME);
      if (empty($mailerConfig['frequency'])) {
        $defaultSettings = $settings->getAllDefaults();
        $mailerConfig['frequency'] = $defaultSettings['mta']['frequency'];
      }
      $sinceSeconds = (int)$mailerConfig['frequency']['interval'] * Mailer::SENDING_LIMIT_INTERVAL_MULTIPLIER;
    }
    $sinceDate = date('Y-m-d H:i:s', time() - $sinceSeconds);
    $mailerLog = self::getMailerLog($mailerLog);

    return (int)array_sum(
      array_filter(
        (array)$mailerLog['sent'],
        function($date) use ($sinceDate): bool {
          return $sinceDate <= $date;
        },
        \ARRAY_FILTER_USE_KEY
      )
    );
  }

  /**
   * Clears "sent" section of the mailer log from outdated entries.
   *
   * @param MailerLogData|null $mailerLog
   * @return MailerLogData
   */
  private static function removeOutdatedSentInformationFromMailerlog(array $mailerLog = null): array {

    $settings = SettingsController::getInstance();
    $mailerConfig = $settings->get(Mailer::MAILER_CONFIG_SETTING_NAME);
    $frequencyInterval = (int)$mailerConfig['frequency']['interval'] * Mailer::SENDING_LIMIT_INTERVAL_MULTIPLIER;
    $sinceDate = self::sentEntriesDate(time() - $frequencyInterval);
    $mailerLog = self::getMailerLog($mailerLog);

    $mailerLog['sent'] = array_filter(
      (array)$mailerLog['sent'],
      function($date) use ($sinceDate): bool {
        return $sinceDate <= $date;
      },
      \ARRAY_FILTER_USE_KEY
    );
    return $mailerLog;
  }

  /**
   * @param int|null $timestamp
   * @return string
   */
  private static function sentEntriesDate(int $timestamp = null): string {

    return date('Y-m-d H:i:s', $timestamp ?? time());
  }

  /**
   * @param MailerLogData|null $mailerLog
   * @return bool
   */
  public static function isSendingPaused(array $mailerLog = null): bool {
    $mailerLog = self::getMailerLog($mailerLog);
    return $mailerLog['status'] === self::STATUS_PAUSED;
  }

  /**
   * @param MailerLogData|null $mailerLog
   * @return bool
   */
  public static function isSendingWaitingForRetry(array $mailerLog = null): bool {
    $mailerLog = self::getMailerLog($mailerLog);
    $retryAt = $mailerLog['retry_at'] ?? null;
    return $retryAt && (time() <= $retryAt);
  }
}
