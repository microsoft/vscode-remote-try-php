<?php declare(strict_types = 1);

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class ConfirmInterestBeforeDeactivation {
  private $assets_url;
  private $external_template_image_url;
  private $template_image_url;

  public function __construct(
    $assets_url
  ) {
    $this->assets_url = $assets_url;
    $this->external_template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/confirm-interest-before-deactivation';
    $this->template_image_url = $this->assets_url . '/img/blank_templates';
  }

  public function get(): array {
    return [
      'name' => __('Confirm interest before deactivation', 'mailpoet'),
      'categories' => json_encode(['re_engagement', 'all']),
      'readonly' => 1,
      'thumbnail' => $this->getThumbnail(),
      'body' => json_encode($this->getBody()),
    ];
  }

  private function getBody(): array {
    return [
      'content' => [
        'type' => 'container',
        'columnLayout' => false,
        'orientation' => 'vertical',
        'image' => [
          'src' => null,
          'display' => 'scale',
        ],
        'styles' => [
          'block' => [
            'backgroundColor' => 'transparent',
          ],
        ],
        'blocks' => [
          [
            'type' => 'container',
            'columnLayout' => false,
            'orientation' => 'horizontal',
            'image' => [
              'src' => null,
              'display' => 'scale',
            ],
            'styles' => [
              'block' => [
                'backgroundColor' => '#ffffff',
              ],
            ],
            'blocks' => [
              [
                'type' => 'container',
                'columnLayout' => false,
                'orientation' => 'vertical',
                'image' => [
                  'src' => null,
                  'display' => 'scale',
                ],
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '30px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/fake-logo.png',
                    'alt' => __('Fake logo', 'mailpoet'),
                    'fullWidth' => false,
                    'width' => '598px',
                    'height' => '71px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
                    'type' => 'text',
                    'text' => __('<p style="text-align: left;">Hi [subscriber:firstname | default:there],</p><p style="text-align: left;"></p>
                                  <p style="text-align: left;"><span>You have subscribed to receive email updates from us, but as we\'re not sure if you\'re reading our emails, we\'d like to ask - <strong>are you still interested in hearing from us?</strong> If yes, please reconfirm your subscription by clicking the button below:</span></p>', 'mailpoet'),
                  ],
                  [
                    'type' => 'button',
                    'text' => __('Yes, keep me subscribed!', 'mailpoet'),
                    'url' => '[link:subscription_re_engage_url]',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#2ea1cd',
                        'borderColor' => '#0074a2',
                        'borderWidth' => '1px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '288px',
                        'lineHeight' => '40px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Verdana',
                        'fontSize' => '16px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
                    'type' => 'text',
                    'text' => '<p><span class="prismjs css-1xfvm4v" data-code-lang="" data-ds--code--code-block="">' . __("If you're no longer interested, that's completely fine. You don't need to take any action. Our system will automatically unsubscribe you soon from all of our future content.", 'mailpoet') . '</span></p>',
                  ],
                ],
              ],
            ],
          ],
          [
            'type' => 'container',
            'columnLayout' => false,
            'orientation' => 'horizontal',
            'image' => [
              'src' => null,
              'display' => 'scale',
            ],
            'styles' => [
              'block' => [
                'backgroundColor' => '#ffffff',
              ],
            ],
            'blocks' => [
              [
                'type' => 'container',
                'columnLayout' => false,
                'orientation' => 'vertical',
                'image' => [
                  'src' => null,
                  'display' => 'scale',
                ],
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  [
                    'type' => 'text',
                    'text' => '<p><strong><em></em></strong></p><p><strong><em>' . __('The MailPoet Team', 'mailpoet') . '</em></strong></p>',
                  ],
                  [
                    'type' => 'footer',
                    'text' => '<p><a href="[link:subscription_unsubscribe_url]">' . __('Unsubscribe', 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]">' . __('Manage subscription', 'mailpoet') . '</a><br />' . __('Add your postal address here!', 'mailpoet') . '</p>',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                      ],
                      'text' => [
                        'fontColor' => '#222222',
                        'fontFamily' => 'Arial',
                        'fontSize' => '12px',
                        'textAlign' => 'left',
                      ],
                      'link' => [
                        'fontColor' => '#6cb7d4',
                        'textDecoration' => 'none',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
        ],
      ],
      'globalStyles' => [
        'text' => [
          'fontColor' => '#000000',
          'fontFamily' => 'Arial',
          'fontSize' => '15px',
          'lineHeight' => '1.6',
        ],
        'h1' => [
          'fontColor' => '#111111',
          'fontFamily' => 'Trebuchet MS',
          'fontSize' => '30px',
          'lineHeight' => '1.6',
        ],
        'h2' => [
          'fontColor' => '#222222',
          'fontFamily' => 'Trebuchet MS',
          'fontSize' => '24px',
          'lineHeight' => '1.6',
        ],
        'h3' => [
          'fontColor' => '#333333',
          'fontFamily' => 'Trebuchet MS',
          'fontSize' => '22px',
          'lineHeight' => '1.6',
        ],
        'link' => [
          'fontColor' => '#21759B',
          'textDecoration' => 'underline',
        ],
        'wrapper' => [
          'backgroundColor' => '#ffffff',
        ],
        'body' => [
          'backgroundColor' => '#ffffff',
        ],
      ],
    ];
  }

  private function getThumbnail(): string {
    return $this->external_template_image_url . '/thumbnail.20211026.jpg';
  }
}
