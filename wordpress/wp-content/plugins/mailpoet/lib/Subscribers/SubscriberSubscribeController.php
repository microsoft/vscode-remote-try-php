<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\FormEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\SubscriberTagEntity;
use MailPoet\Form\FormsRepository;
use MailPoet\Form\Util\FieldNameObfuscator;
use MailPoet\NotFoundException;
use MailPoet\Segments\SubscribersFinder;
use MailPoet\Settings\SettingsController;
use MailPoet\Statistics\StatisticsFormsRepository;
use MailPoet\Subscription\Captcha\CaptchaConstants;
use MailPoet\Subscription\Captcha\CaptchaSession;
use MailPoet\Subscription\Captcha\Validator\BuiltInCaptchaValidator;
use MailPoet\Subscription\Captcha\Validator\RecaptchaValidator;
use MailPoet\Subscription\Captcha\Validator\ValidationError;
use MailPoet\Subscription\Throttling as SubscriptionThrottling;
use MailPoet\Tags\TagRepository;
use MailPoet\UnexpectedValueException;
use MailPoet\WP\Functions as WPFunctions;

class SubscriberSubscribeController {
  /** @var FormsRepository */
  private $formsRepository;

  /** @var CaptchaSession */
  private $captchaSession;

  /** @var FieldNameObfuscator */
  private $fieldNameObfuscator;

  /** @var SettingsController */
  private $settings;

  /** @var RequiredCustomFieldValidator */
  private $requiredCustomFieldValidator;

  /** @var SubscriberActions */
  private $subscriberActions;

  /** @var WPFunctions */
  private $wp;

  /** @var SubscriptionThrottling */
  private $throttling;

  /** @var StatisticsFormsRepository */
  private $statisticsFormsRepository;

  /** @var SubscribersFinder */
  private $subscribersFinder;

  /** @var TagRepository */
  private $tagRepository;

  /** @var SubscriberTagRepository */
  private $subscriberTagRepository;
  /** @var BuiltInCaptchaValidator  */
  private $builtInCaptchaValidator;

  /** @var RecaptchaValidator  */
  private $recaptchaValidator;

  public function __construct(
    CaptchaSession $captchaSession,
    SubscriberActions $subscriberActions,
    SubscribersFinder $subscribersFinder,
    SubscriptionThrottling $throttling,
    FieldNameObfuscator $fieldNameObfuscator,
    RequiredCustomFieldValidator $requiredCustomFieldValidator,
    SettingsController $settings,
    FormsRepository $formsRepository,
    StatisticsFormsRepository $statisticsFormsRepository,
    TagRepository $tagRepository,
    SubscriberTagRepository $subscriberTagRepository,
    WPFunctions $wp,
    BuiltInCaptchaValidator $builtInCaptchaValidator,
    RecaptchaValidator $recaptchaValidator
  ) {
    $this->formsRepository = $formsRepository;
    $this->captchaSession = $captchaSession;
    $this->requiredCustomFieldValidator = $requiredCustomFieldValidator;
    $this->fieldNameObfuscator = $fieldNameObfuscator;
    $this->settings = $settings;
    $this->subscriberActions = $subscriberActions;
    $this->subscribersFinder = $subscribersFinder;
    $this->wp = $wp;
    $this->throttling = $throttling;
    $this->statisticsFormsRepository = $statisticsFormsRepository;
    $this->tagRepository = $tagRepository;
    $this->subscriberTagRepository = $subscriberTagRepository;
    $this->builtInCaptchaValidator = $builtInCaptchaValidator;
    $this->recaptchaValidator = $recaptchaValidator;
  }

  public function subscribe(array $data): array {
    $form = $this->getForm($data);

    if (!empty($data['email'])) {
      throw new UnexpectedValueException(__('Please leave the first field empty.', 'mailpoet'));
    }

    $captchaSettings = $this->settings->get('captcha');
    $data = $this->initCaptcha($captchaSettings, $form, $data);
    $data = $this->deobfuscateFormPayload($data);

    try {
      $this->requiredCustomFieldValidator->validate($data, $form);
    } catch (\Exception $e) {
      throw new UnexpectedValueException($e->getMessage());
    }

    $segmentIds = $this->getSegmentIds($form, $data['segments'] ?? []);
    unset($data['segments']);

    $meta = $this->validateCaptcha($captchaSettings, $data);
    if (isset($meta['error'])) {
      return $meta;
    }

    // only accept fields defined in the form
    $formFieldIds = array_filter(array_map(function (array $formField): ?string {
      if (!isset($formField['id'])) {
        return null;
      }
      return is_numeric($formField['id']) ? "cf_{$formField['id']}" : $formField['id'];
    }, $form->getBlocksByTypes(FormEntity::FORM_FIELD_TYPES)));
    $data = array_intersect_key($data, array_flip($formFieldIds));

    // make sure we don't allow too many subscriptions with the same ip address
    $timeout = $this->throttling->throttle();

    if ($timeout > 0) {
      $timeToWait = $this->throttling->secondsToTimeString($timeout);
      $meta['refresh_captcha'] = true;
      // translators: %s is the amount of time the user has to wait.
      $meta['error'] = sprintf(__('You need to wait %s before subscribing again.', 'mailpoet'), $timeToWait);
      return $meta;
    }

    /**
     * Fires before a subscription gets created.
     * To interrupt the subscription process, you can throw an MailPoet\Exception.
     * The error message will then be displayed to the user.
     *
     * @param array      $data       The subscription data.
     * @param array      $segmentIds The segment IDs the user gets subscribed to.
     * @param FormEntity $form       The form the user used to subscribe.
     */
    $this->wp->doAction('mailpoet_subscription_before_subscribe', $data, $segmentIds, $form);

    [$subscriber, $subscriptionMeta] = $this->subscriberActions->subscribe($data, $segmentIds);

    if (!empty($captchaSettings['type']) && $captchaSettings['type'] === CaptchaConstants::TYPE_BUILTIN) {
      // Captcha has been verified, invalidate the session vars
      $this->captchaSession->reset();
    }

    // record form statistics
    $this->statisticsFormsRepository->record($form, $subscriber);

    $formSettings = $form->getSettings();

    // add tags to subscriber if they are filled
    $this->addTagsToSubscriber($formSettings['tags'] ?? [], $subscriber);

    // Confirmation email failed. We want to show the error message
    if ($subscriptionMeta['confirmationEmailResult'] instanceof \Exception) {
      $meta['error'] = $subscriptionMeta['confirmationEmailResult']->getMessage();
      return $meta;
    }

    if (!empty($formSettings['on_success'])) {
      if ($formSettings['on_success'] === 'page') {
        // redirect to a page on a success, pass the page url in the meta
        $meta['redirect_url'] = $this->wp->getPermalink($formSettings['success_page']);
      } else if ($formSettings['on_success'] === 'url') {
        $meta['redirect_url'] = $formSettings['success_url'];
      }
    }

    return $meta;
  }

  /**
   * Checks if the subscriber is subscribed to any segments in the form
   *
   * @param  FormEntity       $form       The form entity
   * @param  SubscriberEntity $subscriber The subscriber entity
   * @return bool True if the subscriber is subscribed to any of the segments in the form
   */
  public function isSubscribedToAnyFormSegments(FormEntity $form, SubscriberEntity $subscriber): bool {
    $formSegments = array_merge($form->getSegmentBlocksSegmentIds(), $form->getSettingsSegmentIds());

    $subscribersFound = $this->subscribersFinder->findSubscribersInSegments([$subscriber->getId()], $formSegments);
    if (!empty($subscribersFound)) return true;

    return false;
  }

  private function deobfuscateFormPayload($data): array {
    return $this->fieldNameObfuscator->deobfuscateFormPayload($data);
  }

  private function initCaptcha(?array $captchaSettings, FormEntity $form, array $data): array {
    if (
      !$captchaSettings
      || !isset($captchaSettings['type'])
      || $captchaSettings['type'] !== CaptchaConstants::TYPE_BUILTIN
    ) {
      return $data;
    }

    $captchaSessionId = isset($data['captcha_session_id']) ? $data['captcha_session_id'] : null;
    $this->captchaSession->init($captchaSessionId);
    if (!isset($data['captcha'])) {
      // Save form data to session
      $this->captchaSession->setFormData(array_merge($data, ['form_id' => $form->getId()]));
    } elseif ($this->captchaSession->getFormData()) {
      // Restore form data from session
      $data = array_merge($this->captchaSession->getFormData(), ['captcha' => $data['captcha']]);
    }
    return $data;
  }

  private function validateCaptcha($captchaSettings, $data): array {
    if (empty($captchaSettings['type'])) {
      return [];
    }
    try {
      if ($captchaSettings['type'] === CaptchaConstants::TYPE_BUILTIN) {
        $this->builtInCaptchaValidator->validate($data);
      }
      if (CaptchaConstants::isReCaptcha($captchaSettings['type'])) {
        $this->recaptchaValidator->validate($data);
      }
    } catch (ValidationError $error) {
      return $error->getMeta();
    }
    return [];
  }

  private function getSegmentIds(FormEntity $form, array $segmentIds): array {

    // If form contains segment selection blocks allow only segments ids configured in those blocks
    $segmentBlocksSegmentIds = $form->getSegmentBlocksSegmentIds();
    if (!empty($segmentBlocksSegmentIds)) {
      $segmentIds = array_intersect($segmentIds, $segmentBlocksSegmentIds);
    } else {
      $segmentIds = $form->getSettingsSegmentIds();
    }

    if (empty($segmentIds)) {
      throw new UnexpectedValueException(__('Please select a list.', 'mailpoet'));
    }

    return $segmentIds;
  }

  private function getForm(array $data): FormEntity {
    $formId = (isset($data['form_id']) ? (int)$data['form_id'] : false);
    $form = $this->formsRepository->findOneById($formId);

    if (!$form) {
      throw new NotFoundException(__('Please specify a valid form ID.', 'mailpoet'));
    }

    return $form;
  }

  /**
   * @param string[] $tagNames
   */
  private function addTagsToSubscriber(array $tagNames, SubscriberEntity $subscriber): void {
    foreach ($tagNames as $tagName) {
      $tag = $this->tagRepository->createOrUpdate(['name' => $tagName]);

      $subscriberTag = $subscriber->getSubscriberTag($tag);
      if (!$subscriberTag) {
        $subscriberTag = new SubscriberTagEntity($tag, $subscriber);
        $subscriber->getSubscriberTags()->add($subscriberTag);
        $this->subscriberTagRepository->persist($subscriberTag);
        $this->subscriberTagRepository->flush();
      }
    }
  }
}
