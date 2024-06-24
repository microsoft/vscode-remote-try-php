<?php declare(strict_types = 1);

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\SendingQueue\Tasks\Shortcodes;
use MailPoet\Mailer\Mailer;
use MailPoet\Mailer\MailerFactory;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Helpers;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class DisabledMailFunctionNotice {

  const DISABLED_MAIL_FUNCTION_CHECK = 'disabled_mail_function_check';

  const QUEUE_DISABLED_MAIL_FUNCTION_CHECK = 'queue_disabled_mail_function_check';

  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  /** @var SubscribersFeature */
  private $subscribersFeature;

  /** @var MailerFactory */
  private $mailerFactory;

  private $isInQueueForChecking = false;

  public function __construct(
    WPFunctions $wp,
    SettingsController $settings,
    SubscribersFeature $subscribersFeature,
    MailerFactory $mailerFactory
  ) {
    $this->settings = $settings;
    $this->wp = $wp;
    $this->subscribersFeature = $subscribersFeature;
    $this->mailerFactory = $mailerFactory;
  }

  public function init($shouldDisplay): ?string {
    $shouldDisplay = $shouldDisplay && $this->shouldCheckMisconfiguredFunction() && $this->checkRequirements();
    if (!$shouldDisplay) {
      return null;
    }

    return $this->display();
  }

  private function checkRequirements(): bool {
    if ($this->isInQueueForChecking) {
      $this->settings->set(self::QUEUE_DISABLED_MAIL_FUNCTION_CHECK, false);
    }

    $sendingMethod = $this->settings->get('mta.method', SettingsController::DEFAULT_SENDING_METHOD);
    $isPhpMailSendingMethod = $sendingMethod === Mailer::METHOD_PHPMAIL;

    if (!$isPhpMailSendingMethod) {
      return false; // fails requirements check
    }

    $functionName = 'mail';
    $isMailFunctionDisabled = $this->isFunctionDisabled($functionName);

    if ($isMailFunctionDisabled) {
      $this->settings->set(DisabledMailFunctionNotice::DISABLED_MAIL_FUNCTION_CHECK, true);
      return true;
    }

    $isMailFunctionProperlyConfigured = $this->testMailFunctionIsCorrectlyConfigured();

    return !$isMailFunctionProperlyConfigured;
  }

  /*
   * Check MisConfigured Function
   *
   * This method will cause this class to only display the notice if the settings option
   *
   * disabled_mail_function_check === true
   * or
   * queue_disabled_mail_function_check === true
   * or
   * Totally disabled when wp filter `mailpoet_display_disabled_mail_function_notice` === false
   *
   */
  public function shouldCheckMisconfiguredFunction(): bool {
    $shouldCheck = $this->wp->applyFilters('mailpoet_display_disabled_mail_function_notice', true);
    $this->isInQueueForChecking = $this->settings->get(self::QUEUE_DISABLED_MAIL_FUNCTION_CHECK, false);

    return $shouldCheck && (
      $this->settings->get(self::DISABLED_MAIL_FUNCTION_CHECK, false) ||
      $this->isInQueueForChecking
      );
  }

  public function isFunctionDisabled(string $function): bool {
    $result = function_exists($function) && is_callable($function, false);
    return !$result;
  }

  private function display(): string {
    $header = $this->getHeader();

    $body = $this->getBody();

    $button = $this->getConnectMailPoetButton();

    $message = $header . $body . $button;

    Notice::displayWarning($message, '', self::DISABLED_MAIL_FUNCTION_CHECK, false);

    return $message;
  }

  private function getHeader(): string {
    return '<h4>' . __('Get ready to send your first campaign.', 'mailpoet') . '</h4>';
  }

  private function getBody(): string {
    $bodyText = __('Connect your website with MailPoet, and start sending for free. Reach inboxes, not spam boxes. [link]Why am I seeing this?[/link]', 'mailpoet');

    $bodyWithReplacedLink = Helpers::replaceLinkTags($bodyText, 'https://kb.mailpoet.com/article/396-disabled-mail-function', [
      'target' => '_blank',
    ]);

    return '<p>' . $bodyWithReplacedLink . '</p>';
  }

  private function getConnectMailPoetButton(): string {
    $subscribersCount = $this->subscribersFeature->getSubscribersCount();
    $buttonLink = "https://account.mailpoet.com/?s={$subscribersCount}&utm_source=mailpoet&utm_medium=plugin&utm_campaign=disabled_mail_function";
    $link = $this->wp->escAttr($buttonLink);
    return '<p><a target="_blank" href="' . $link . '" class="button button-primary">' . __('Connect MailPoet', 'mailpoet') . '</a></p>';
  }

  /*
   * Test Mail Function Is Correctly Configured
   *
   * This is a workaround for detecting the user PHP mail() function is Correctly Configured and not disabled by the host
   */
  private function testMailFunctionIsCorrectlyConfigured(): bool {
    if ($this->settings->get(DisabledMailFunctionNotice::DISABLED_MAIL_FUNCTION_CHECK, false)) {
      return false; // skip sending mail again
    }

    $replyToAddress = $this->settings->get('reply_to.address');
    $senderAddress = $this->settings->get('sender.address');

    $mailBody = "Hi there! \n

Your website ([site:homepage_link]) sent you this email to confirm that it can send emails.
If you're reading this email, then it works! You can now continue sending marketing emails with MailPoet! \n

MailPoet on [site:homepage_link]";

    $body = Shortcodes::process($mailBody, null, null, null, null);

    $sendTestMailData = [
      'mailer' => $this->settings->get('mta'),
      'newsletter' => [
        'subject' => 'MailPoet can deliver your marketing emails!',
        'body' => [
          'html' => nl2br($body),
          'text' => $body,
        ],
      ],
      'subscriber' => empty($replyToAddress) ? $senderAddress : $replyToAddress,
    ];

    $sendMailResult = $this->sendTestMail($sendTestMailData);

    if (!$sendMailResult) {
      // Error with PHP mail() function
      // keep displaying notice
      $this->settings->set(DisabledMailFunctionNotice::DISABLED_MAIL_FUNCTION_CHECK, true);
    }

    return $sendMailResult;
  }

  /*
   * Send Test Mail
   * used to check for valid PHP mail()
   *
   * returns true if valid and okay
   * else returns false if invalid.
   *
   * We determine the mail function is invalid by checking against the Exception error thrown by PHPMailer
   * error message: Could not instantiate mail function.
   *
   * if the error is not equal to error message, we consider it okay.
   */
  public function sendTestMail($data = []): bool {
    try {
      $mailer = $this->mailerFactory->buildMailer(
        $data['mailer'] ?? null,
        $data['sender'] ?? null,
        $data['reply_to'] ?? null
      );
      // report this as 'sending_test' in metadata since this endpoint is only used to test sending methods for now
      $extraParams = [
        'meta' => [
          'email_type' => 'sending_test',
          'subscriber_status' => 'unknown',
          'subscriber_source' => 'administrator',
        ],
      ];
      $result = $mailer->send($data['newsletter'], $data['subscriber'], $extraParams);

      if ($result['response'] === false) {
        $errorMessage = $result['error']->getMessage();
        return !$this->checkForErrorMessage($errorMessage);
      }

    } catch (\Exception $e) {
      $errorMessage = $e->getMessage();
      return !$this->checkForErrorMessage($errorMessage);
    }

    return true;
  }

  private function checkForErrorMessage($errorMessage): bool {
    $phpmailerError = 'Could not instantiate mail function';
    $substringIndex = stripos($errorMessage, $phpmailerError);

    return $substringIndex !== false;
  }
}
