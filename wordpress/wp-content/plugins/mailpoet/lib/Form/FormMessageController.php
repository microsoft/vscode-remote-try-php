<?php declare(strict_types = 1);

namespace MailPoet\Form;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\FormEntity;
use MailPoet\Settings\SettingsController;

class FormMessageController {
  /** @var FormsRepository */
  private $formsRepository;

  /** @var SettingsController */
  private $settings;

  public function __construct(
    FormsRepository $formsRepository,
    SettingsController $settingsController
  ) {
    $this->formsRepository = $formsRepository;
    $this->settings = $settingsController;
  }

  public function updateSuccessMessages(): void {
    $rightMessage = $this->getDefaultSuccessMessage();
    $wrongMessage = (
    $rightMessage === __('Check your inbox or spam folder to confirm your subscription.', 'mailpoet')
      ? __('You’ve been successfully subscribed to our newsletter!', 'mailpoet')
      : __('Check your inbox or spam folder to confirm your subscription.', 'mailpoet')
    );
    /** @var FormEntity[] $forms */
    $forms = $this->formsRepository->findAll();
    foreach ($forms as $form) {
      $settings = $form->getSettings();
      if (isset($settings['success_message']) && $settings['success_message'] === $wrongMessage) {
        $settings['success_message'] = $rightMessage;
        $form->setSettings($settings);
        $this->formsRepository->flush();
      }
    }
  }

  public function getDefaultSuccessMessage(): string {
    if ($this->settings->get('signup_confirmation.enabled')) {
      return __('Check your inbox or spam folder to confirm your subscription.', 'mailpoet');
    }
    return __('You’ve been successfully subscribed to our newsletter!', 'mailpoet');
  }
}
