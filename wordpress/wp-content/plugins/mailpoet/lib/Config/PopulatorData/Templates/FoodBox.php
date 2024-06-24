<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class FoodBox {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
     $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/food_box';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Welcome to FoodBox", 'mailpoet'),
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
          0 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#f4f4f4',
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
                ],
              ],
            ],
          ],
          1 => [
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Food-Delivery-Logo.png',
                    'alt' => 'Food-Delivery-Logo',
                    'fullWidth' => false,
                    'width' => '640px',
                    'height' => '180px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Food-Delivery-App.png',
                    'alt' => 'Food-Delivery-App',
                    'fullWidth' => false,
                    'width' => '640px',
                    'height' => '180px',
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
                        'height' => '40px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'text',
                    'text' => '<h1><strong>Welcome to FoodBox</strong></h1>
                                  <h2><strong>Lorem ipsum dolor sit amet</strong></h2>
                                  <p>Curabitur sollicitudin eros eu cursus sollicitudin. Suspendisse laoreet sollicitudin urna, ut lacinia risus dictum a. Integer a neque eu magna commodo sodales eu eget ante.</p>',
                  ],
                  2 => [
                    'type' => 'button',
                    'text' => 'Get Started',
                    'url' => '',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#7cc119',
                        'borderColor' => '#7cc119',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '100px',
                        'lineHeight' => '40px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Arial',
                        'fontSize' => '14px',
                        'fontWeight' => 'bold',
                        'textAlign' => 'left',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Food-Delivery-Focus.jpg',
                    'alt' => 'Food-Delivery-Focus',
                    'fullWidth' => false,
                    'width' => '800px',
                    'height' => '800px',
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
          5 => [
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
                        'backgroundColor' => 'transparent',
                        'padding' => '31.5px',
                        'borderStyle' => 'solid',
                        'borderWidth' => '2px',
                        'borderColor' => '#e5e5e5',
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
                    'text' => '<h2 style="text-align: center;">Get started in 3 simple steps</h2>',
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
                    'src' => $this->template_image_url . '/Food-Delivery-1-1.png',
                    'alt' => 'Food-Delivery-1',
                    'fullWidth' => false,
                    'width' => '800px',
                    'height' => '250px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  2 => [
                    'type' => 'text',
                    'text' => '<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sollicitudin eros eu cursus sollicitudin.</p>',
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
                    'src' => $this->template_image_url . '/Food-Delivery-2-1.png',
                    'alt' => 'Food-Delivery-2',
                    'fullWidth' => false,
                    'width' => '800px',
                    'height' => '250px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  2 => [
                    'type' => 'text',
                    'text' => '<p style="text-align: center;"><span style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sollicitudin eros eu cursus sollicitudin.</span></p>',
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
                    'src' => $this->template_image_url . '/Food-Delivery-3-1.png',
                    'alt' => 'Food-Delivery-3',
                    'fullWidth' => false,
                    'width' => '800px',
                    'height' => '250px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  2 => [
                    'type' => 'text',
                    'text' => '<p style="text-align: center;"><span style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sollicitudin eros eu cursus sollicitudin.</span></p>',
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
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '30px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'button',
                    'text' => 'Get Started',
                    'url' => '',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#7cc119',
                        'borderColor' => '#7cc119',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '100px',
                        'lineHeight' => '40px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Arial',
                        'fontSize' => '14px',
                        'fontWeight' => 'bold',
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  2 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '25px',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          9 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#4599da',
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
                        'height' => '30px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'text',
                    'text' => '<p style="text-align: center; font-size: 14px;"><strong><span style="color: #ffffff;">Link 1 - Link 2 - Link 3 - Link 4</span></strong></p>',
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
                        'height' => '24px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'social',
                    'iconSet' => 'full-symbol-grey',
                    'icons' => [
                      0 => [
                        'type' => 'socialIcon',
                        'iconType' => 'facebook',
                        'link' => 'http://www.facebook.com',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Facebook.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Facebook',
                      ],
                      1 => [
                        'type' => 'socialIcon',
                        'iconType' => 'twitter',
                        'link' => 'http://www.twitter.com',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Twitter.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Twitter',
                      ],
                      2 => [
                        'type' => 'socialIcon',
                        'iconType' => 'youtube',
                        'link' => 'http://www.youtube.com',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Youtube.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Youtube',
                      ],
                      3 => [
                        'type' => 'socialIcon',
                        'iconType' => 'instagram',
                        'link' => 'http://instagram.com',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Instagram.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Instagram',
                      ],
                      4 => [
                        'type' => 'socialIcon',
                        'iconType' => 'website',
                        'link' => '',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Website.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Website',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          10 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#4599da',
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
                        'height' => '25px',
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          11 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#f4f4f4',
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
                    'type' => 'footer',
                    'text' => '<p><a href="[link:subscription_unsubscribe_url]">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]">' . __("Manage your subscription", 'mailpoet') . '</a><br />' . __("Add your postal address here!", 'mailpoet') . '</p>',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                      ],
                      'text' => [
                        'fontColor' => '#222222',
                        'fontFamily' => 'Arial',
                        'fontSize' => '12px',
                        'textAlign' => 'center',
                      ],
                      'link' => [
                        'fontColor' => '#6cb7d4',
                        'textDecoration' => 'none',
                      ],
                    ],
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
            ],
          ],
        ],
      ],
      'globalStyles' => [
        'text' => [
          'fontColor' => '#000000',
          'fontFamily' => 'Arial',
          'fontSize' => '12px',
        ],
        'h1' => [
          'fontColor' => '#4599da',
          'fontFamily' => 'Arial',
          'fontSize' => '26px',
        ],
        'h2' => [
          'fontColor' => '#878787',
          'fontFamily' => 'Arial',
          'fontSize' => '18px',
        ],
        'h3' => [
          'fontColor' => '#333333',
          'fontFamily' => 'Arial',
          'fontSize' => '14px',
        ],
        'link' => [
          'fontColor' => '#4599da',
          'textDecoration' => 'underline',
        ],
        'wrapper' => [
          'backgroundColor' => '#ffffff',
        ],
        'body' => [
          'backgroundColor' => '#f4f4f4',
        ],
      ],
    ];
  }

  private function getThumbnail() {
    return $this->template_image_url . '/thumbnail.20190411-1500.jpg';
  }
}
