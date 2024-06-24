<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class AppWelcome {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/app_welcome';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("App Welcome", 'mailpoet'),
      'categories' => json_encode(['welcome', 'all']),
      'readonly' => 1,
      'thumbnail' => $this->getThumbnail(),
      'body' => json_encode($this->getBody()),
    ];
  }

  private function getBody() {
    return [
      'content' => [
        'type' => 'container',
        'orientation' => 'vertical',
        'styles' => [
          'block' => [
            'backgroundColor' => 'transparent',
          ],
        ],
        'blocks' => [
          [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#eeeeee',
              ],
            ],
            'blocks' => [
              [
                'type' => 'container',
                'orientation' => 'vertical',
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
                        'backgroundColor' => '#eeeeee',
                        'height' => '30px',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#32b6c6',
              ],
            ],
            'blocks' => [
              [
                'type' => 'container',
                'orientation' => 'vertical',
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
                        'height' => '40px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/App-Signup-Logo-1.png',
                    'alt' => 'App-Signup-Logo',
                    'fullWidth' => false,
                    'width' => '80px',
                    'height' => '80px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
                    'type' => 'text',
                    'text' => '<h1 style="text-align: center; margin: 0;"><strong>Welcome to Appy</strong></h1><p style="text-align: center; margin: 0;"><span style="color: #ffffff;">Let\'s get started!</span></p>',
                  ],

                  [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/App-Signup-Header.png',
                    'alt' => 'App-Signup-Header',
                    'fullWidth' => false,
                    'width' => '1280px',
                    'height' => '500px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],

          [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#ffffff',
              ],
            ],
            'blocks' => [
              [
                'type' => 'container',
                'orientation' => 'vertical',
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
                        'height' => '40px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'text',
                    'text' => '<p style="text-align: center;">Hi [subscriber:firstname | default:subscriber],</p>
                                  <p style="text-align: center;"></p>
                                  <p style="text-align: center;">In MailPoet, you can write emails in plain text, just like in a regular email. This can make your email newsletters more personal and attention-grabbing.</p>
                                  <p style="text-align: center;"></p>
                                  <p style="text-align: center;">Is this too simple? You can still style your text with basic formatting, like <strong>bold</strong> or <em>italics.</em></p>
                                  <p style="text-align: center;"></p>
                                  <p style="text-align: center;">Finally, you can also add a call-to-action button between 2 blocks of text, like this:</p>',
                  ],
                ],
              ],
            ],
          ],
          [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#ffffff',
              ],
            ],
            'blocks' => [
              [
                'type' => 'container',
                'orientation' => 'vertical',
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
                        'height' => '23px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'button',
                    'text' => 'Get Started Here',
                    'url' => '',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#32b6c6',
                        'borderColor' => '#32b6c6',
                        'borderWidth' => '0px',
                        'borderRadius' => '40px',
                        'borderStyle' => 'solid',
                        'width' => '188px',
                        'lineHeight' => '50px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Arial',
                        'fontSize' => '18px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '35px',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => 'transparent',
              ],
            ],
            'blocks' => [
              [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/App-Signup-Team.jpg',
                    'alt' => 'App-Signup-Team',
                    'fullWidth' => true,
                    'width' => '1280px',
                    'height' => '700px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#eeeeee',
              ],
            ],
            'blocks' => [
              [
                'type' => 'container',
                'orientation' => 'vertical',
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
                    'src' => $this->template_image_url . '/App-Signup-Logo-Footer.png',
                    'alt' => 'App-Signup-Logo-Footer',
                    'fullWidth' => false,
                    'width' => '50px',
                    'height' => '50px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
                    'type' => 'text',
                    'text' => '<p style="text-align: center; font-size: 12px;"><strong>Appy</strong></p>
                                <p style="text-align: center; font-size: 12px;"><span>Address Line 1</span></p>
                                <p style="text-align: center; font-size: 12px;"><span>Address Line 2</span></p>
                                <p style="text-align: center; font-size: 12px;"><span>City</span></p>
                                <p style="text-align: center; font-size: 12px;"><a href="[link:subscription_unsubscribe_url]">' . __("Unsubscribe", 'mailpoet') . '</a><span> | </span><a href="[link:subscription_manage_url]">' . __("Manage your subscription", 'mailpoet') . '</a></p>',
                  ],
                  [
                    'type' => 'social',
                    'iconSet' => 'full-symbol-color',
                    'icons' => [
                      [
                        'type' => 'socialIcon',
                        'iconType' => 'facebook',
                        'link' => 'http://www.facebook.com',
                        'image' => $this->social_icon_url . '/06-full-symbol-color/Facebook.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Facebook',
                      ],
                      [
                        'type' => 'socialIcon',
                        'iconType' => 'twitter',
                        'link' => 'http://www.twitter.com',
                        'image' => $this->social_icon_url . '/06-full-symbol-color/Twitter.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Twitter',
                      ],
                      [
                        'type' => 'socialIcon',
                        'iconType' => 'youtube',
                        'link' => 'http://www.youtube.com',
                        'image' => $this->social_icon_url . '/06-full-symbol-color/Youtube.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Youtube',
                      ],
                      [
                        'type' => 'socialIcon',
                        'iconType' => 'instagram',
                        'link' => 'http://instagram.com',
                        'image' => $this->social_icon_url . '/06-full-symbol-color/Instagram.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Instagram',
                      ],
                    ],
                  ],
                  [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '40px',
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
          'fontColor' => '#404040',
          'fontFamily' => 'Arial',
          'fontSize' => '15px',
        ],
        'h1' => [
          'fontColor' => '#ffffff',
          'fontFamily' => 'Arial',
          'fontSize' => '26px',
        ],
        'h2' => [
          'fontColor' => '#404040',
          'fontFamily' => 'Arial',
          'fontSize' => '22px',
        ],
        'h3' => [
          'fontColor' => '#32b6c6',
          'fontFamily' => 'Arial',
          'fontSize' => '18px',
        ],
        'link' => [
          'fontColor' => '#32b6c6',
          'textDecoration' => 'underline',
        ],
        'wrapper' => [
          'backgroundColor' => '#ffffff',
        ],
        'body' => [
          'backgroundColor' => '#eeeeee',
        ],
      ],
    ];
  }

  private function getThumbnail() {
    return $this->template_image_url . '/thumbnail.20190411-1500.jpg';
  }
}
