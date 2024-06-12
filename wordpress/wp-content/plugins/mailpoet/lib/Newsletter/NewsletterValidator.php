<?php declare(strict_types = 1);

namespace MailPoet\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Features\FeaturesController;
use MailPoet\Services\Bridge;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Validator\ValidationException;

class NewsletterValidator {

  /** @var Bridge */
  private $bridge;

  /** @var TrackingConfig */
  private $trackingConfig;

  /** @var FeaturesController */
  private $featuresController;

  public function __construct(
    Bridge $bridge,
    TrackingConfig $trackingConfig,
    FeaturesController $featuresController
  ) {
    $this->bridge = $bridge;
    $this->trackingConfig = $trackingConfig;
    $this->featuresController = $featuresController;
  }

  public function validate(NewsletterEntity $newsletterEntity): ?string {
    if (
      $this->featuresController->isSupported(FeaturesController::GUTENBERG_EMAIL_EDITOR)
      && $newsletterEntity->getWpPostId() !== null
    ) {
      // Temporarily skip validation for emails created via Gutenberg editor
      return null;
    }
    try {
      $this->validateBody($newsletterEntity);
      $this->validateUnsubscribeRequirements($newsletterEntity);
      $this->validateReEngagementRequirements($newsletterEntity);
      $this->validateAutomaticLatestContentRequirements($newsletterEntity);
    } catch (ValidationException $exception) {
      return $exception->getMessage();
    }
    return null;
  }

  private function validateUnsubscribeRequirements(NewsletterEntity $newsletterEntity): void {
    if (!$this->bridge->isMailpoetSendingServiceEnabled()) {
      return;
    }
    $content = $newsletterEntity->getContent();
    $hasUnsubscribeUrl = strpos($content, '[link:subscription_unsubscribe_url]') !== false;
    $hasUnsubscribeLink = strpos($content, '[link:subscription_unsubscribe]') !== false;

    if (!$hasUnsubscribeLink && !$hasUnsubscribeUrl) {
      throw new ValidationException(__('All emails must include an "Unsubscribe" link. Add a footer widget to your email to continue.', 'mailpoet'));
    }
  }

  private function validateBody(NewsletterEntity $newsletterEntity): void {
    $emptyBodyErrorMessage = __('Poet, please add prose to your masterpiece before you send it to your followers.', 'mailpoet');
    $content = $newsletterEntity->getContent();

    if ($content === '') {
      throw new ValidationException($emptyBodyErrorMessage);
    }

    $contentBlocks = $newsletterEntity->getBody()['content']['blocks'] ?? [];
    if (count($contentBlocks) < 1) {
      throw new ValidationException($emptyBodyErrorMessage);
    }
  }

  private function validateReEngagementRequirements(NewsletterEntity $newsletterEntity): void {
    if ($newsletterEntity->getType() !== NewsletterEntity::TYPE_RE_ENGAGEMENT) {
      return;
    }

    if (strpos($newsletterEntity->getContent(), '[link:subscription_re_engage_url]') === false) {
      throw new ValidationException(__('A re-engagement email must include a link with [link:subscription_re_engage_url] shortcode.', 'mailpoet'));
    }

    if (!$this->trackingConfig->isEmailTrackingEnabled()) {
      throw new ValidationException(__('Re-engagement emails are disabled because open and click tracking is disabled in MailPoet → Settings → Advanced.', 'mailpoet'));
    }
  }

  private function validateAutomaticLatestContentRequirements(NewsletterEntity $newsletterEntity) {
    if ($newsletterEntity->getType() !== NewsletterEntity::TYPE_NOTIFICATION) {
      return;
    }
    $content = $newsletterEntity->getContent();
    if (
      strpos($content, '"type":"automatedLatestContent"') === false &&
      strpos($content, '"type":"automatedLatestContentLayout"') === false
    ) {
      throw new ValidationException(_x('Please add an “Automatic Latest Content” widget to the email from the right sidebar.', '(Please reuse the current translation used for the string “Automatic Latest Content”) This Error message is displayed when a user tries to send a “Post Notification” email without any “Automatic Latest Content” widget inside', 'mailpoet'));
    }
  }
}
