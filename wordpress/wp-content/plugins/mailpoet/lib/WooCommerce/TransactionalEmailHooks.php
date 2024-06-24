<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Settings\SettingsController;
use MailPoet\WooCommerce\TransactionalEmails\Renderer;
use MailPoet\WP\Functions as WPFunctions;

class TransactionalEmailHooks {
  /** @var WPFunctions */
  private $wp;

  /** @var SettingsController */
  private $settings;

  /** @var Renderer */
  private $renderer;

  /** @var NewslettersRepository */
  private $newsletterRepository;

  /** @var TransactionalEmails */
  private $transactionalEmails;

  public function __construct(
    WPFunctions $wp,
    SettingsController $settings,
    Renderer $renderer,
    NewslettersRepository $newsletterRepository,
    TransactionalEmails $transactionalEmails
  ) {
    $this->wp = $wp;
    $this->settings = $settings;
    $this->renderer = $renderer;
    $this->newsletterRepository = $newsletterRepository;
    $this->transactionalEmails = $transactionalEmails;
  }

  public function useTemplateForWoocommerceEmails() {
    $this->wp->addAction('woocommerce_email', function($wcEmails) {
      /** @var callable */
      $emailHeaderCallback = [$wcEmails, 'email_header'];
      /** @var callable */
      $emailFooterCallback = [$wcEmails, 'email_footer'];
      $this->wp->removeAction('woocommerce_email_header', $emailHeaderCallback);
      $this->wp->removeAction('woocommerce_email_footer', $emailFooterCallback);
      $this->wp->addAction('woocommerce_email_header', function($emailHeading) {
        $newsletterEntity = $this->getNewsletter();
        if ($newsletterEntity) {
          $this->renderer->render($newsletterEntity, $emailHeading);
          // The HTML is generated from a $newsletter entity and can be considered safe
          // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPressDotOrg.sniffs.OutputEscaping.UnescapedOutputParameter
          echo $this->renderer->getHTMLBeforeContent();
        }
      });
      $this->wp->addAction('woocommerce_email_footer', function() {
        // The HTML is generated from a $newsletter entity and can be considered safe
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPressDotOrg.sniffs.OutputEscaping.UnescapedOutputParameter
        echo $this->renderer->getHTMLAfterContent();
      });
      $this->wp->addAction('woocommerce_email_styles', [$this->renderer, 'prefixCss']);
    });
  }

  private function getNewsletter(): ?NewsletterEntity {
    if (empty($this->settings->get(TransactionalEmails::SETTING_EMAIL_ID))) {
      return null;
    }
    $newsletter = $this->newsletterRepository->findOneById($this->settings->get(TransactionalEmails::SETTING_EMAIL_ID));
    if (!$newsletter) {
      // the newsletter should always be present in the database, if it s not we shouldn't keep using this feature
      // we need to recreate the newsletter and turn off the feature
      $this->transactionalEmails->init();
      $this->settings->set('woocommerce.use_mailpoet_editor', false);
    }
    return $newsletter;
  }

  public function overrideStylesForWooEmails() {
    $this->wp->addAction('option_woocommerce_email_background_color', function($value) {
      $newsletter = $this->getNewsletter();
      if (!$newsletter instanceof NewsletterEntity) return $value;
      return $newsletter->getGlobalStyle('body', 'backgroundColor') ?? $value;
    });
    $this->wp->addAction('option_woocommerce_email_base_color', function($value) {
      $newsletter = $this->getNewsletter();
      if (!$newsletter instanceof NewsletterEntity) return $value;
      return $newsletter->getGlobalStyle('woocommerce', 'brandingColor') ?? $value;
    });
    $this->wp->addAction('option_woocommerce_email_body_background_color', function($value) {
      $newsletter = $this->getNewsletter();
      if (!$newsletter instanceof NewsletterEntity) return $value;
      return $newsletter->getGlobalStyle('wrapper', 'backgroundColor') ?? $value;
    });
    $this->wp->addAction('option_woocommerce_email_text_color', function($value) {
      $newsletter = $this->getNewsletter();
      if (!$newsletter instanceof NewsletterEntity) return $value;
      return $newsletter->getGlobalStyle('text', 'fontColor') ?? $value;
    });
  }
}
