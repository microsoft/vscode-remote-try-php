<?php declare(strict_types = 1);

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Renderer\Renderer as NewsletterRenderer;
use MailPoet\Settings\SettingsController;

class ConfirmationEmailCustomizer {
  const SETTING_EMAIL_ID = 'signup_confirmation.transactional_email_id';
  const SETTING_ENABLE_EMAIL_CUSTOMIZER = 'signup_confirmation.use_mailpoet_editor';

  /** @var SettingsController */
  private $settings;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var NewsletterRenderer */
  private $renderer;

  public function __construct(
    SettingsController $settings,
    NewslettersRepository $newslettersRepository,
    NewsletterRenderer $renderer
  ) {
    $this->settings = $settings;
    $this->newslettersRepository = $newslettersRepository;
    $this->renderer = $renderer;
  }

  public function init() {
    $savedEmailId = (bool)$this->settings->get(self::SETTING_EMAIL_ID, false);
    if (!$savedEmailId) {
      $email = $this->createNewsletter();
      if (is_null($email)) return;

      $this->settings->set(self::SETTING_EMAIL_ID, $email->getId());
    }
  }

  private function createNewsletter(): ?NewsletterEntity {
    $emailTemplate = $this->fetchEmailTemplate();

    if (empty($emailTemplate)) {
      // if it's not able to fetch email template, don't bother creating newsletter
      return null;
    }

    $newsletter = new NewsletterEntity;
    $newsletter->setType(NewsletterEntity::TYPE_CONFIRMATION_EMAIL_CUSTOMIZER);
    $newsletter->setSubject($this->settings->get('signup_confirmation.subject', 'Confirm your subscription to [site:title]'));
    $newsletter->setBody($emailTemplate);
    $this->newslettersRepository->persist($newsletter);
    $this->newslettersRepository->flush();
    return $newsletter;
  }

  private function fetchEmailTemplate() {
    $templateUrl = Env::$libPath . '/Subscribers/ConfirmationEmailTemplate/template-confirmation.json';
    $templateString = file_get_contents($templateUrl);
    $templateArr = json_decode((string)$templateString, true);
    $template = (array)$templateArr;
    return $template['body'];
  }

  public function getNewsletter(): NewsletterEntity {
    $savedEmailId = $this->settings->get(self::SETTING_EMAIL_ID, false);

    if (empty($savedEmailId)) {
      $this->init();
      $savedEmailId = $this->settings->get(self::SETTING_EMAIL_ID);
    }

    $newsletter = $this->newslettersRepository->findOneById($savedEmailId);
    if (!$newsletter) {
      // the newsletter should always be present in the database, if it s not we shouldn't keep using this feature
      // we need to recreate the newsletter
      $this->settings->set(self::SETTING_EMAIL_ID, false); // reset
      $this->init();
      return $this->getNewsletter();
    }
    return $newsletter;
  }

  public function render(NewsletterEntity $newsletter): ?array {
    $renderedContent = $this->renderer->renderAsPreview($newsletter);

    if (empty($renderedContent)) {
      return null;
    }

    $renderedContent['subject'] = $newsletter->getSubject();

    return $renderedContent;
  }
}
