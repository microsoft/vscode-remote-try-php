<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AutomaticEmails;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class AutomaticEmails {
  const FILTER_PREFIX = 'mailpoet_automatic_email_';

  /** @var WPFunctions */
  private $wp;

  /** @var array|null */
  private $automaticEmails;

  /** @var AutomaticEmailFactory */
  private $automaticEmailFactory;

  public function __construct(
    WPFunctions $wp,
    AutomaticEmailFactory $automaticEmailFactory
  ) {
    $this->wp = $wp;
    $this->automaticEmailFactory = $automaticEmailFactory;
  }

  public function init() {
    $instance = $this->automaticEmailFactory->createWooCommerceEmail();
    $instance->init();
  }

  public function getAutomaticEmails() {
    global $wp_filter; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    if ($this->automaticEmails) {
      return $this->automaticEmails;
    }

    // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $registeredGroups = preg_grep('!^' . self::FILTER_PREFIX . '(.*?)$!', array_keys($wp_filter));

    if (empty($registeredGroups)) return null;

    $automaticEmails = [];
    foreach ($registeredGroups as $group) {
      $automaticEmail = $this->wp->applyFilters($group, []);

      if (
        !$this->validateAutomaticEmailDataFields($automaticEmail) ||
        !$this->validateAutomaticEmailEventsDataFields($automaticEmail['events'])
      ) {
        continue;
      }

      // keys associative events array by slug
      $automaticEmail['events'] = array_column($automaticEmail['events'], null, 'slug');
      // keys associative automatic email array by slug
      $automaticEmails[$automaticEmail['slug']] = $automaticEmail;
    }

    $this->automaticEmails = $automaticEmails;

    return $automaticEmails;
  }

  public function getAutomaticEmailBySlug($emailSlug) {
    $automaticEmails = $this->getAutomaticEmails();

    if (empty($automaticEmails)) return null;

    foreach ($automaticEmails as $email) {
      if (!empty($email['slug']) && $email['slug'] === $emailSlug) return $email;
    }

    return null;
  }

  public function getAutomaticEmailEventBySlug($emailSlug, $eventSlug) {
    $automaticEmail = $this->getAutomaticEmailBySlug($emailSlug);

    if (empty($automaticEmail)) return null;

    foreach ($automaticEmail['events'] as $event) {
      if (!empty($event['slug']) && $event['slug'] === $eventSlug) return $event;
    }

    return null;
  }

  public function validateAutomaticEmailDataFields(array $automaticEmail) {
    $requiredFields = [
      'slug',
      'title',
      'description',
      'events',
    ];

    foreach ($requiredFields as $field) {
      if (empty($automaticEmail[$field])) return false;
    }

    return true;
  }

  public function validateAutomaticEmailEventsDataFields(array $automaticEmailEvents) {
    $requiredFields = [
      'slug',
      'title',
      'description',
      'listingScheduleDisplayText',
    ];

    foreach ($automaticEmailEvents as $event) {
      $validEvent = array_diff($requiredFields, array_keys($event));
      if (!empty($validEvent)) return false;
    }

    return true;
  }

  public function unregisterAutomaticEmails() {
    global $wp_filter; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $registeredGroups = preg_grep('!^' . self::FILTER_PREFIX . '(.*?)$!', array_keys($wp_filter));

    if (empty($registeredGroups)) return null;

    $self = $this;
    array_map(function($group) use($self) {
      $self->wp->removeAllFilters($group);
    }, $registeredGroups);

    $this->automaticEmails = null;
  }
}
