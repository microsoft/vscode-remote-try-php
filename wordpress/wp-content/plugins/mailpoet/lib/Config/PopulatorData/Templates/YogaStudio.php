<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class YogaStudio {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
     $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/yoga_studio';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Yoga Studio", 'mailpoet'),
      'categories' => json_encode(['standard', 'all']),
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
          0 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#f8f8f8',
              ],
            ],
            'blocks' => [
              0 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#1e2937',
                        'height' => '20px',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          1 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#ffffff',
              ],
            ],
            'blocks' => [
              0 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/yoga-1.png',
                    'alt' => 'yoga-1',
                    'fullWidth' => true,
                    'width' => '1280px',
                    'height' => '740px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '30px',
                      ],
                    ],
                  ],
                  2 => [
                    'type' => 'text',
                    'text' => '<h2 style="text-align: center;"><strong>Here\'s your classes for this week:</strong></h2>',
                  ],
                  3 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '20px',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          2 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => 'transparent',
              ],
            ],
            'blocks' => [
              0 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'text',
                    'text' => '<h3><strong><span style="color: #83bd31;"><em>Weekdays</em></span></strong></h3>
                      <p><strong>Monday</strong>: 7am, 9am, 11am, 3pm and 5pm.</p>
                      <p><strong>Tuesday</strong>: 7am, 9am, 11am, 3pm and 5pm.</p>
                      <p><strong>Wednesday</strong>: 7am, 9am, 11am, 3pm and 5pm.</p>
                      <p><strong>Thursday</strong>:&nbsp;CLOSED FOR PRIVATE CLASS.</p>
                      <p><strong>Friday</strong>: 7am, 9am, 11am,&nbsp;and 3pm.</p>',
                  ],
                ],
              ],
              1 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'text',
                    'text' => '<h3><strong><span style="color: #83bd31;"><em>Weekend</em></span></strong></h3>
                      <p><strong>Saturday</strong>: 7am, 9am, 11am, 3pm and 5pm.</p>
                      <p><strong>Sunday</strong>: 7am, 9am, 11am, 3pm and 5pm.</p>
                      <p></p>',
                  ],
                ],
              ],
            ],
          ],
          3 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => 'transparent',
              ],
            ],
            'blocks' => [
              0 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'divider',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'padding' => '22px',
                        'borderStyle' => 'solid',
                        'borderWidth' => '1px',
                        'borderColor' => '#d5d5d5',
                      ],
                    ],
                  ],
                  2 => [
                    'type' => 'text',
                    'text' => '<h3 style="text-align: center;"><span style="font-weight: 600;">Meet the instructors</span></h3>',
                  ],
                ],
              ],
            ],
          ],
          4 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => 'transparent',
              ],
            ],
            'blocks' => [
              0 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/yoga-person-1.png',
                    'alt' => 'yoga-person-1',
                    'fullWidth' => false,
                    'width' => '400px',
                    'height' => '400px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  2 => [
                    'type' => 'text',
                    'text' => '<h3 style="text-align: center;"><span style="color: #83bd31;"><span style="font-weight: 600;">Maria Smith</span></span></h3>
                      <p style="text-align: center;">Nullam hendrerit feugiat feugiat. Praesent mollis ante lacus, quis tempor leo sagittis vel. Donec sagittis eros at felis venenatis ultricies.</p>',
                  ],
                  3 => [
                    'type' => 'button',
                    'text' => 'Find Out More',
                    'url' => '',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#83bd31',
                        'borderColor' => '#83bd31',
                        'borderWidth' => '1px',
                        'borderRadius' => '40px',
                        'borderStyle' => 'solid',
                        'width' => '180px',
                        'lineHeight' => '30px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Arial',
                        'fontSize' => '14px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  4 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '30px',
                      ],
                    ],
                  ],
                ],
              ],
              1 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/yoga-person-2.png',
                    'alt' => 'yoga-person-2',
                    'fullWidth' => false,
                    'width' => '400px',
                    'height' => '400px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  2 => [
                    'type' => 'text',
                    'text' => '<h3 style="text-align: center;"><span style="color: #83bd31;"><span style="font-weight: 600;">Fiona&nbsp;Davies</span></span></h3>
                      <p style="text-align: center;">Nullam hendrerit feugiat feugiat. Praesent mollis ante lacus, quis tempor leo sagittis vel. Donec sagittis eros at felis venenatis ultricies.</p>',
                  ],
                  3 => [
                    'type' => 'button',
                    'text' => 'Find Out More',
                    'url' => '',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#83bd31',
                        'borderColor' => '#83bd31',
                        'borderWidth' => '1px',
                        'borderRadius' => '40px',
                        'borderStyle' => 'solid',
                        'width' => '180px',
                        'lineHeight' => '30px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Arial',
                        'fontSize' => '14px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  4 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '30px',
                      ],
                    ],
                  ],
                ],
              ],
              2 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/yoga-person-3.png',
                    'alt' => 'yoga-person-3',
                    'fullWidth' => false,
                    'width' => '400px',
                    'height' => '400px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  2 => [
                    'type' => 'text',
                    'text' => '<h3 style="text-align: center;"><span style="color: #83bd31;"><span style="font-weight: 600;">Matthew&nbsp;Johnson</span></span></h3>
                       <p style="text-align: center;">Nullam hendrerit feugiat feugiat. Praesent mollis ante lacus, quis tempor leo sagittis vel. Donec sagittis eros at felis venenatis ultricies.</p>',
                  ],
                  3 => [
                    'type' => 'button',
                    'text' => 'Find Out More',
                    'url' => '',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#83bd31',
                        'borderColor' => '#83bd31',
                        'borderWidth' => '1px',
                        'borderRadius' => '40px',
                        'borderStyle' => 'solid',
                        'width' => '180px',
                        'lineHeight' => '31px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Arial',
                        'fontSize' => '14px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  4 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '30px',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          5 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#83bd31',
              ],
            ],
            'blocks' => [
              0 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '40px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'text',
                    'text' => '<p><strong>Pose of the week:</strong></p>
                      <h2>Virabhadrasana I</h2>
                      <p>The myth is that the powerful priest Daksha made a great yagna (ritual sacrifice) but did not invite his youngest daughter Sati and her husband Shiva, the supreme ruler of the universe. Sati found out and decided to go alone to the yagna.</p>
                      <p></p>
                      <p>When she arrived, Sati entered into an argument with her father. Unable to withstand his insults, she spoke a vow to her father, &ldquo;Since it was you who gave me this body, I no longer wish to be associated with it.&rdquo;</p>',
                  ],
                  2 => [
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
              1 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '40px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/yoga-pose.png',
                    'alt' => 'yoga-pose',
                    'fullWidth' => false,
                    'width' => '400px',
                    'height' => '400px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  2 => [
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
          6 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#ffffff',
              ],
            ],
            'blocks' => [
              0 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '40px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'text',
                    'text' => '<h3 style="text-align: center;"><span style="font-weight: 600;">Quote of the week</span></h3>',
                  ],
                  2 => [
                    'type' => 'text',
                    'text' => '<h2 style="text-align: center;"><em>Be a lamp to yourself. Be your own confidence. Hold on to the truth within yourself as to the only truth.</em></h2>
                      <p style="text-align: center;"><span style="font-family: Arial, sans-serif; font-size: 14px; text-align: center; color: #999999;">Buddha</span><em></em></p>',
                  ],
                  3 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#ffffff',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  4 => [
                    'type' => 'divider',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#d5d5d5',
                        'padding' => '15px',
                        'borderStyle' => 'solid',
                        'borderWidth' => '1px',
                        'borderColor' => '#b3b3b3',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          7 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#d5d5d5',
              ],
            ],
            'blocks' => [
              0 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/yoga-logo-small.png',
                    'alt' => 'yoga-logo-small',
                    'fullWidth' => false,
                    'width' => '50px',
                    'height' => '50px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'social',
                    'iconSet' => 'full-symbol-black',
                    'icons' => [
                      0 => [
                        'type' => 'socialIcon',
                        'iconType' => 'facebook',
                        'link' => 'http://www.facebook.com',
                        'image' => $this->social_icon_url . '/07-full-symbol-black/Facebook.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Facebook',
                      ],
                      1 => [
                        'type' => 'socialIcon',
                        'iconType' => 'twitter',
                        'link' => 'http://www.twitter.com',
                        'image' => $this->social_icon_url . '/07-full-symbol-black/Twitter.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Twitter',
                      ],
                      2 => [
                        'type' => 'socialIcon',
                        'iconType' => 'instagram',
                        'link' => 'http://instagram.com',
                        'image' => $this->social_icon_url . '/07-full-symbol-black/Instagram.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Instagram',
                      ],
                      3 => [
                        'type' => 'socialIcon',
                        'iconType' => 'youtube',
                        'link' => 'http://www.youtube.com',
                        'image' => $this->social_icon_url . '/07-full-symbol-black/Youtube.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Youtube',
                      ],
                    ],
                  ],
                ],
              ],
              1 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'text',
                    'text' => '<p style="font-size: 13px; text-align: center;"><strong>Yoga Studio</strong></p>
                      <p style="font-size: 11px; text-align: center;">Address Line 1</p>
                      <p style="font-size: 11px; text-align: center;">Address Line 2</p>
                      <p style="font-size: 11px; text-align: center;">City/Town</p>
                      <p style="font-size: 11px; text-align: center;">Country</p>',
                  ],
                ],
              ],
              2 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'footer',
                    'text' => '<p><a href="[link:subscription_unsubscribe_url]">' . __("Unsubscribe", 'mailpoet') . '</a></p><p><a href="[link:subscription_manage_url]">' . __("Manage your subscription", 'mailpoet') . '</a></p>',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                      ],
                      'text' => [
                        'fontColor' => '#222222',
                        'fontFamily' => 'Arial',
                        'fontSize' => '11px',
                        'textAlign' => 'center',
                      ],
                      'link' => [
                        'fontColor' => '#000000',
                        'textDecoration' => 'underline',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          8 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => 'transparent',
              ],
            ],
            'blocks' => [
              0 => [
                'type' => 'container',
                'orientation' => 'vertical',
                'styles' => [
                  'block' => [
                    'backgroundColor' => 'transparent',
                  ],
                ],
                'blocks' => [
                  0 => [
                    'type' => 'divider',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#d5d5d5',
                        'padding' => '13px',
                        'borderStyle' => 'solid',
                        'borderWidth' => '1px',
                        'borderColor' => '#aaaaaa',
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
          'fontColor' => '#1e2937',
          'fontFamily' => 'Arial',
          'fontSize' => '13px',
        ],
        'h1' => [
          'fontColor' => '#1e2937',
          'fontFamily' => 'Arial',
          'fontSize' => '30px',
        ],
        'h2' => [
          'fontColor' => '#1e2937',
          'fontFamily' => 'Arial',
          'fontSize' => '24px',
        ],
        'h3' => [
          'fontColor' => '#1e2937',
          'fontFamily' => 'Arial',
          'fontSize' => '20px',
        ],
        'link' => [
          'fontColor' => '#83bd31',
          'textDecoration' => 'underline',
        ],
        'wrapper' => [
          'backgroundColor' => '#ffffff',
        ],
        'body' => [
          'backgroundColor' => '#1e2937',
        ],
      ],
    ];
  }

  private function getThumbnail() {
    return $this->template_image_url . '/thumbnail.20190411-1500.jpg';
  }
}
