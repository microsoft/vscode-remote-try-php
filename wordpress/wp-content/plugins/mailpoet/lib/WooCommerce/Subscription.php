<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscribers\ConfirmationEmailMailer;
use MailPoet\Subscribers\Source;
use MailPoet\Subscribers\SubscriberSegmentRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class Subscription {
  const CHECKOUT_OPTIN_INPUT_NAME = 'mailpoet_woocommerce_checkout_optin';
  const CHECKOUT_OPTIN_PRESENCE_CHECK_INPUT_NAME = 'mailpoet_woocommerce_checkout_optin_present';
  const OPTIN_ENABLED_SETTING_NAME = 'woocommerce.optin_on_checkout.enabled';
  const OPTIN_SEGMENTS_SETTING_NAME = 'woocommerce.optin_on_checkout.segments';
  const OPTIN_MESSAGE_SETTING_NAME = 'woocommerce.optin_on_checkout.message';

  private $allowedHtml = [
    'input' => [
      'type' => true,
      'name' => true,
      'id' => true,
      'class' => true,
      'value' => true,
      'checked' => true,
    ],
    'span' => [
      'class' => true,
    ],
    'label' => [
      'class' => true,
      'data-automation-id' => true,
      'for' => true,
    ],
    'p' => [
      'class' => true,
      'id' => true,
      'data-priority' => true,
    ],
  ];

  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  /** @var Helper */
  private $wcHelper;

  /** @var ConfirmationEmailMailer */
  private $confirmationEmailMailer;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var SubscriberSegmentRepository */
  private $subscriberSegmentRepository;

  public function __construct(
    SettingsController $settings,
    ConfirmationEmailMailer $confirmationEmailMailer,
    WPFunctions $wp,
    Helper $wcHelper,
    SubscribersRepository $subscribersRepository,
    SegmentsRepository $segmentsRepository,
    SubscriberSegmentRepository $subscriberSegmentRepository
  ) {
    $this->settings = $settings;
    $this->wp = $wp;
    $this->wcHelper = $wcHelper;
    $this->confirmationEmailMailer = $confirmationEmailMailer;
    $this->subscribersRepository = $subscribersRepository;
    $this->segmentsRepository = $segmentsRepository;
    $this->subscriberSegmentRepository = $subscriberSegmentRepository;
  }

  public function extendWooCommerceCheckoutForm() {
    $this->hideAutomateWooOptinCheckbox();
    $inputName = self::CHECKOUT_OPTIN_INPUT_NAME;
    $checked = false;
    if (!empty($_POST[self::CHECKOUT_OPTIN_INPUT_NAME])) {
      $checked = true;
    }
    $labelString = $this->settings->get(self::OPTIN_MESSAGE_SETTING_NAME);
    $template = (string)$this->wp->applyFilters(
      'mailpoet_woocommerce_checkout_optin_template',
      wp_kses(
        $this->getSubscriptionField($inputName, $checked, $labelString),
        $this->allowedHtml
      ),
      $inputName,
      $checked,
      $labelString
    );
    // The template has been sanitized above and can be considered safe.
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPressDotOrg.sniffs.OutputEscaping.UnescapedOutputParameter
    echo $template;
    if ($template) {
      $field = $this->getSubscriptionPresenceCheckField();
      echo wp_kses($field, $this->allowedHtml);
    }
  }

  private function getSubscriptionField($inputName, $checked, $labelString) {
    $checked = checked($checked, true, false);

    return '<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox" data-automation-id="woo-commerce-subscription-opt-in">
      <input id="mailpoet_woocommerce_checkout_optin" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" ' . $checked . ' type="checkbox" name="' . $this->wp->escAttr($inputName) . '" value="1" />
      <span>' . $this->wp->escHtml($labelString) . '</span>
    </label>';
  }

  private function getSubscriptionPresenceCheckField() {
    $field = $this->wcHelper->woocommerceFormField(
      self::CHECKOUT_OPTIN_PRESENCE_CHECK_INPUT_NAME,
      [
        'type' => 'hidden',
        'return' => true,
      ],
      1
    );
    if ($field) {
      return $field;
    }
    // Workaround for older WooCommerce versions (below 4.6.0) that don't support hidden fields
    // We can remove it after we drop support of older WooCommerce
    $field = $this->wcHelper->woocommerceFormField(
      self::CHECKOUT_OPTIN_PRESENCE_CHECK_INPUT_NAME,
      [
        'type' => 'text',
        'return' => true,
      ],
      1
    );
    return str_replace('type="text"', 'type="hidden"', $field);
  }

  public function subscribeOnOrderPay($orderId) {
    $wcOrder = $this->wcHelper->wcGetOrder($orderId);
    if (!$wcOrder instanceof \WC_Order) {
      return null;
    }

    $data['billing_email'] = $wcOrder->get_billing_email();
    $this->subscribeOnCheckout($orderId, $data);
  }

  public function subscribeOnCheckout($orderId, $data) {
    $this->triggerAutomateWooOptin();
    if (empty($data['billing_email'])) {
      // no email in posted order data
      return null;
    }

    $subscriber = $this->subscribersRepository->findOneBy(
      ['email' => $data['billing_email'], 'isWoocommerceUser' => 1]
    );

    if (!$subscriber) {
      // no subscriber: WooCommerce sync didn't work
      return null;
    }

    $checkoutOptin = !empty($_POST[self::CHECKOUT_OPTIN_INPUT_NAME]);

    return $this->handleSubscriberOptin($subscriber, $checkoutOptin);
  }

  /**
   * Subscribe a subscriber.
   *
   * @param SubscriberEntity $subscriber Subscriber object
   * @param bool $shouldSubscribe Whether the subscriber should be subscribed
   */
  public function handleSubscriberOptin(SubscriberEntity $subscriber, bool $shouldSubscribe): bool {
    $wcSegment = $this->segmentsRepository->getWooCommerceSegment();

    $segmentIds = (array)$this->settings->get(self::OPTIN_SEGMENTS_SETTING_NAME, []);
    $moreSegmentsToSubscribe = [];
    if (!empty($segmentIds)) {
      $moreSegmentsToSubscribe = $this->segmentsRepository->findBy(['id' => $segmentIds]);
    }
    $signupConfirmation = $this->settings->get('signup_confirmation');

    if ($shouldSubscribe) {
      $subscriber->setSource(Source::WOOCOMMERCE_CHECKOUT);

      if (
        ($subscriber->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED)
        || ((bool)$signupConfirmation['enabled'] === false)
      ) {
        $this->subscribe($subscriber);
      } else {
        $this->requireSubscriptionConfirmation($subscriber);
      }

      $this->subscriberSegmentRepository->subscribeToSegments($subscriber, array_merge([$wcSegment], $moreSegmentsToSubscribe));

      return true;
    } else {
      return false;
    }
  }

  private function hideAutomateWooOptinCheckbox(): void {
    if (!$this->wp->isPluginActive('automatewoo/automatewoo.php')) {
      return;
    }
    // Hide AutomateWoo checkout opt-in so we won't end up with two opt-ins
    $this->wp->removeAction(
      'woocommerce_checkout_after_terms_and_conditions',
      ['AutomateWoo\Frontend', 'output_checkout_optin_checkbox']
    );
  }

  private function triggerAutomateWooOptin(): void {
    if (
      !$this->wp->isPluginActive('automatewoo/automatewoo.php')
      || empty($_POST[self::CHECKOUT_OPTIN_INPUT_NAME])
    ) {
      return;
    }
    // Emulate checkout opt-in triggering for AutomateWoo
    $_POST['automatewoo_optin'] = 'On';
  }

  private function subscribe(SubscriberEntity $subscriber) {
    $subscriber->setStatus(SubscriberEntity::STATUS_SUBSCRIBED);
    if (empty($subscriber->getConfirmedIp()) && empty($subscriber->getConfirmedAt())) {
      $subscriber->setConfirmedIp(Helpers::getIP());
      $subscriber->setConfirmedAt(new Carbon());
    }

    $this->subscribersRepository->persist($subscriber);
    $this->subscribersRepository->flush();
  }

  private function requireSubscriptionConfirmation(SubscriberEntity $subscriber) {
    $subscriber->setStatus(SubscriberEntity::STATUS_UNCONFIRMED);
    $this->subscribersRepository->persist($subscriber);
    $this->subscribersRepository->flush();

    try {
      $this->confirmationEmailMailer->sendConfirmationEmailOnce($subscriber);
    } catch (\Exception $e) {
      // ignore errors
    }
  }
}
