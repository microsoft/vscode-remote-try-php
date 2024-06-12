<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscription;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoet\Entities\FormEntity;
use MailPoet\Form\FormsRepository;
use MailPoet\Form\Renderer as FormRenderer;
use MailPoet\Form\Util\Styles;
use MailPoet\Subscription\Captcha\CaptchaSession;
use MailPoet\Util\Url as UrlHelper;

class CaptchaFormRenderer {
  /** @var UrlHelper */
  private $urlHelper;

  /** @var CaptchaSession */
  private $captchaSession;

  /** @var SubscriptionUrlFactory */
  private $subscriptionUrlFactory;

  /** @var FormRenderer */
  private $formRenderer;

  /** @var FormsRepository */
  private $formsRepository;

  /** @var Styles */
  private $styles;

  public function __construct(
    UrlHelper $urlHelper,
    CaptchaSession $captchaSession,
    SubscriptionUrlFactory $subscriptionUrlFactory,
    FormsRepository $formsRepository,
    FormRenderer $formRenderer,
    Styles $styles
  ) {
    $this->urlHelper = $urlHelper;
    $this->captchaSession = $captchaSession;
    $this->subscriptionUrlFactory = $subscriptionUrlFactory;
    $this->formRenderer = $formRenderer;
    $this->formsRepository = $formsRepository;
    $this->styles = $styles;
  }

  public function getCaptchaPageTitle() {
    return __("Confirm you’re not a robot", 'mailpoet');
  }

  public function getCaptchaPageContent($sessionId) {

    $this->captchaSession->init($sessionId);
    $captchaSessionForm = $this->captchaSession->getFormData();
    $showSuccessMessage = !empty($_GET['mailpoet_success']);
    $showErrorMessage = !empty($_GET['mailpoet_error']);
    $formId = 0;
    if (isset($captchaSessionForm['form_id'])) {
      $formId = (int)$captchaSessionForm['form_id'];
    } elseif ($showSuccessMessage) {
      $formId = (int)$_GET['mailpoet_success'];
    } elseif ($showErrorMessage) {
      $formId = (int)$_GET['mailpoet_error'];
    }

    $formModel = $this->formsRepository->findOneById($formId);
    if (!$formModel instanceof FormEntity) {
      return false;
    }

    $fields = [
      [
        'id' => 'captcha',
        'type' => 'text',
        'params' => [
          'label' => __('Type in the characters you see in the picture above:', 'mailpoet'),
          'value' => '',
          'obfuscate' => false,
        ],
      ],
    ];

    $submitBlocks = $formModel->getBlocksByTypes(['submit']);
    $submitLabel = count($submitBlocks) && $submitBlocks[0]['params']['label'] ? $submitBlocks[0]['params']['label'] : __('Subscribe', 'mailpoet');
    $form = array_merge(
      $fields,
      [
        [
          'id' => 'submit',
          'type' => 'submit',
          'params' => [
            'label' => $submitLabel,
          ],
        ],
      ]
    );

    if ($showSuccessMessage) {
      // Display a success message in a no-JS flow
      return $this->renderFormMessages($formModel, $showSuccessMessage);
    }

    $formHtml = '<form method="POST" ' .
      'action="' . admin_url('admin-post.php?action=mailpoet_subscription_form') . '" ' .
      'class="mailpoet_form mailpoet_captcha_form" ' .
      'id="mailpoet_captcha_form" ' .
      'novalidate>';
    $formHtml .= '<input type="hidden" name="data[form_id]" value="' . $formId . '" />';
    $formHtml .= '<input type="hidden" name="data[captcha_session_id]" value="' . htmlspecialchars((string)$this->captchaSession->getId()) . '" />';
    $formHtml .= '<input type="hidden" name="api_version" value="v1" />';
    $formHtml .= '<input type="hidden" name="endpoint" value="subscribers" />';
    $formHtml .= '<input type="hidden" name="mailpoet_method" value="subscribe" />';
    $formHtml .= '<input type="hidden" name="mailpoet_redirect" ' .
      'value="' . htmlspecialchars($this->urlHelper->getCurrentUrl(), ENT_QUOTES) . '" />';

    $width = 220;
    $height = 60;
    $captchaUrl = $this->subscriptionUrlFactory->getCaptchaImageUrl($width, $height, $this->captchaSession->getId());
    $mp3CaptchaUrl = $this->subscriptionUrlFactory->getCaptchaAudioUrl($this->captchaSession->getId());

    $reloadIcon = Env::$assetsUrl . '/img/icons/image-rotate.svg';
    $playIcon = Env::$assetsUrl . '/img/icons/controls-volumeon.svg';
    $formHtml .= '<div class="mailpoet_form_hide_on_success">';
    $formHtml .= '<p class="mailpoet_paragraph">';
    $formHtml .= '<img class="mailpoet_captcha" src="' . $captchaUrl . '" width="' . $width . '" height="' . $height . '" title="' . esc_attr__('CAPTCHA', 'mailpoet') . '" />';
    $formHtml .= '</p>';
    $formHtml .= '<button type="button" class="mailpoet_icon_button mailpoet_captcha_update" title="' . esc_attr(__('Reload CAPTCHA', 'mailpoet')) . '"><img src="' . $reloadIcon . '" alt="" /></button>';
    $formHtml .= '<button type="button" class="mailpoet_icon_button mailpoet_captcha_audio" title="' . esc_attr(__('Play CAPTCHA', 'mailpoet')) . '"><img src="' . $playIcon . '" alt="" /></button>';
    $formHtml .= '<audio class="mailpoet_captcha_player">';
    $formHtml .= '<source src="' . $mp3CaptchaUrl . '" type="audio/mpeg">';
    $formHtml .= '</audio>';

    // subscription form
    $formHtml .= $this->formRenderer->renderBlocks($form, [], null, $honeypot = false);
    $formHtml .= '</div>';
    $formHtml .= $this->renderFormMessages($formModel, $showSuccessMessage, $showErrorMessage);
    $formHtml .= '</form>';
    $formHtml .= '<style>' . $this->styles->renderFormMessageStyles(
      $formModel,
      '#mailpoet_captcha_form'
    ) . '</style>';
    return $formHtml;
  }

  private function renderFormMessages(
    FormEntity $formModel,
    $showSuccessMessage = false,
    $showErrorMessage = false
  ) {
    $settings = $formModel->getSettings() ?? [];
    $formHtml = '<div class="mailpoet_message" role="alert" aria-live="assertive">';
    $formHtml .= '<p class="mailpoet_validate_success" ' . ($showSuccessMessage ? '' : ' style="display:none;"') . '>' . $settings['success_message'] . '</p>';
    $formHtml .= '<p class="mailpoet_validate_error" ' . ($showErrorMessage ? '' : ' style="display:none;"') . '>' . __('The characters you entered did not match the CAPTCHA image. Please try again with this new image.', 'mailpoet') . '</p>';
    $formHtml .= '</div>';
    return $formHtml;
  }
}
