<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Shortcodes;

if (!defined('ABSPATH')) exit;


use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\NewsletterLinkEntity;

class ShortcodesHelper {
  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  public function __construct(
    CustomFieldsRepository $customFieldsRepository
  ) {
    $this->customFieldsRepository = $customFieldsRepository;
  }

  public function getShortcodes(): array {
    $shortcodes = [
      __('Subscriber', 'mailpoet') => [
        [
          'text' => __('First Name', 'mailpoet'),
          'shortcode' => '[subscriber:firstname | default:reader]',
        ],
        [
          'text' => __('Last Name', 'mailpoet'),
          'shortcode' => '[subscriber:lastname | default:reader]',
        ],
        [
          'text' => __('Email Address', 'mailpoet'),
          'shortcode' => '[subscriber:email]',
        ],
        [
          'text' => __('WordPress User Display Name', 'mailpoet'),
          'shortcode' => '[subscriber:displayname | default:member]',
        ],
        [
          'text' => __('Total Number of Subscribers', 'mailpoet'),
          'shortcode' => '[subscriber:count]',
        ],
      ],
      __('Newsletter', 'mailpoet') => [
        [
          'text' => __('Newsletter Subject', 'mailpoet'),
          'shortcode' => '[newsletter:subject]',
        ],
      ],
      __('Post Notifications', 'mailpoet') => [
        [
          'text' => __('Total Number of Posts or Pages', 'mailpoet'),
          'shortcode' => '[newsletter:total]',
        ],
        [
          'text' => __('Most Recent Post Title', 'mailpoet'),
          'shortcode' => '[newsletter:post_title]',
        ],
        [
          'text' => __('Issue Number', 'mailpoet'),
          'shortcode' => '[newsletter:number]',
        ],
      ],
      __('Date', 'mailpoet') => [
        [
          'text' => __('Current day of the month number', 'mailpoet'),
          'shortcode' => '[date:d]',
        ],
        [
          'text' => __('Current day of the month in ordinal form, i.e. 2nd, 3rd, 4th, etc.', 'mailpoet'),
          'shortcode' => '[date:dordinal]',
        ],
        [
          'text' => __('Full name of current day', 'mailpoet'),
          'shortcode' => '[date:dtext]',
        ],
        [
          'text' => __('Current month number', 'mailpoet'),
          'shortcode' => '[date:m]',
        ],
        [
          'text' => __('Full name of current month', 'mailpoet'),
          'shortcode' => '[date:mtext]',
        ],
        [
          'text' => __('Year', 'mailpoet'),
          'shortcode' => '[date:y]',
        ],
      ],
      __('Links', 'mailpoet') => [
        [
          'text' => __('Unsubscribe link', 'mailpoet'),
          'shortcode' => sprintf(
            '<a target="_blank" href="%s">%s</a>',
            NewsletterLinkEntity::UNSUBSCRIBE_LINK_SHORT_CODE,
            __('Unsubscribe', 'mailpoet')
          ),
        ],
        [
          'text' => __('Edit subscription page link', 'mailpoet'),
          'shortcode' => sprintf(
            '<a target="_blank" href="%s">%s</a>',
            '[link:subscription_manage_url]',
            __('Manage subscription', 'mailpoet')
          ),
        ],
        [
          'text' => __('View in browser link', 'mailpoet'),
          'shortcode' => sprintf(
            '<a target="_blank" href="%s">%s</a>',
            '[link:newsletter_view_in_browser_url]',
            __('View in your browser', 'mailpoet')
          ),
        ],
      ],
      __('Site', 'mailpoet') => [
        [
          'text' => __('Site title', 'mailpoet'),
          'shortcode' => '[site:title]',
        ],
        [
          'text' => __('Homepage link', 'mailpoet'),
          'shortcode' => sprintf(
            '<a target="_blank" href="%s">%s</a>',
            '[site:homepage_url]',
            '[site:title]'
          ),
        ],
        [
          'text' => __('Homepage URL', 'mailpoet'),
          'shortcode' => '[site:homepage_url]',
        ],
      ],
    ];
    $customFields = $this->getCustomFields();
    if (count($customFields) > 0) {
      $shortcodes[__('Subscriber', 'mailpoet')] = array_merge(
        $shortcodes[__('Subscriber', 'mailpoet')],
        $customFields
      );
    }
    return $shortcodes;
  }

  public function getCustomFields(): array {
    $customFields = $this->customFieldsRepository->findAll();
    return array_map(function($customField) {
      return [
        'text' => $customField->getName(),
        'shortcode' => '[subscriber:cf_' . $customField->getId() . ']',
      ];
    }, $customFields);
  }
}
