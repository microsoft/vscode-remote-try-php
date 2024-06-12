<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class Shoes {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
     $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/shoes';
     $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Shoes", 'mailpoet'),
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
                'backgroundColor' => '#f6f6f6',
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
                    'src' => $this->template_image_url . '/Retail-Shoes-Logo.png',
                    'alt' => 'Retail-Shoes-Logo',
                    'fullWidth' => true,
                    'width' => '1280px',
                    'height' => '220px',
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
                    'src' => $this->template_image_url . '/Retail-Shoes-Header.jpg',
                    'alt' => 'Retail-Shoes-Header',
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
          2 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#f1b512',
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
                    'type' => 'text',
                    'text' => '<h2 style="text-align: center;"><span style="color: #614a0d;">Our New Range</span></h2>
                      <p style="text-align: center;"><span style="color: #614a0d;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque cursus aliquam urna, non ultricies diam sagittis sit amet. Etiam tempus a metus sed tincidunt.</span></p>
                      <p style="text-align: center;"><span style="color: #614a0d;">Curabitur fermentum ligula eget lacus aliquam volutpat. Integer sapien neque, laoreet quis lobortis sed, semper eget magna. Suspendisse potentiu.</span></p>',
                  ],
                  2 => [
                    'type' => 'button',
                    'text' => 'Find Out More',
                    'url' => '',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#41c7bf',
                        'borderColor' => '#28a9a2',
                        'borderWidth' => '2px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '160px',
                        'lineHeight' => '40px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Arial',
                        'fontSize' => '16px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  3 => [
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
          3 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#36b0a9',
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
                        'height' => '70px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'text',
                    'text' => '<h2><span style="color: #ffffff;">Handcrafted Shoes</span></h2>
                       <p style="font-size: 14px;"><span><span style="color: #ffffff;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque cursus aliquam urna, non ultricies diam sagittis sit amet. Etiam tempus a metus sed tincidunt. Curabitur fermentum ligula eget lacus aliquam volutpat.</span></span></p>',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Retail-Shoes-Boxes-1.jpg',
                    'alt' => 'Retail-Shoes-Boxes-1',
                    'fullWidth' => true,
                    'width' => '700px',
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
          4 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#36b0a9',
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
                    'src' => $this->template_image_url . '/Retail-Shoes-Boxes-2.jpg',
                    'alt' => 'Retail-Shoes-Boxes-2',
                    'fullWidth' => true,
                    'width' => '700px',
                    'height' => '700px',
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
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '70px',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'text',
                    'text' => '<h2><span style="color: #ffffff;">Perfect For Any Occasion</span></h2>
                      <p style="font-size: 14px;"><span><span style="color: #ffffff;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque cursus aliquam urna, non ultricies diam sagittis sit amet. Etiam tempus a metus sed tincidunt. Curabitur fermentum ligula eget lacus aliquam volutpat.</span></span></p>',
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
          5 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#f6f6f6',
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
                    'text' => '<h3 style="text-align: center;"><strong>We\'re open every day!</strong></h3>
                        <p style="text-align: center;">Call in any time and we\'ll help you pick the best shoes for any occasion.</p>
                        <p style="text-align: center;">If you\'re not happy, just bring them back to us and we\'ll give you a full refund.</p>',
                  ],
                  2 => [
                    'type' => 'button',
                    'text' => 'Check Out Our Website',
                    'url' => '',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#41c7bf',
                        'borderColor' => '#28a9a2',
                        'borderWidth' => '2px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '220px',
                        'lineHeight' => '40px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Arial',
                        'fontSize' => '16px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  3 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '30px',
                      ],
                    ],
                  ],
                  4 => [
                    'type' => 'divider',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'padding' => '13px',
                        'borderStyle' => 'solid',
                        'borderWidth' => '2px',
                        'borderColor' => '#d3d3d3',
                      ],
                    ],
                  ],
                  5 => [
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
          6 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#f6f6f6',
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
                    'src' => $this->template_image_url . '/Retail-Shoes-Logo-Footer.png',
                    'alt' => 'Retail-Shoes-Logo-Footer',
                    'fullWidth' => true,
                    'width' => '1280px',
                    'height' => '60px',
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
                        'height' => '20px',
                      ],
                    ],
                  ],
                  2 => [
                    'type' => 'text',
                    'text' => '<p style="text-align: center; font-size: 12px;"><span style="color: #999999;">Address Line 1</span></p>
                      <p style="text-align: center; font-size: 12px;"><span style="color: #999999;">Address Line 2</span></p>
                      <p style="text-align: center; font-size: 12px;"><span style="color: #999999;">City</span></p>
                      <p style="text-align: center; font-size: 12px;"><span style="color: #999999;">Country</span></p>',
                  ],
                  3 => [
                    'type' => 'social',
                    'iconSet' => 'grey',
                    'icons' => [
                      0 => [
                        'type' => 'socialIcon',
                        'iconType' => 'facebook',
                        'link' => 'http://www.facebook.com',
                        'image' => $this->social_icon_url . '/02-grey/Facebook.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Facebook',
                      ],
                      1 => [
                        'type' => 'socialIcon',
                        'iconType' => 'twitter',
                        'link' => 'http://www.twitter.com',
                        'image' => $this->social_icon_url . '/02-grey/Twitter.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Twitter',
                      ],
                      2 => [
                        'type' => 'socialIcon',
                        'iconType' => 'instagram',
                        'link' => 'http://instagram.com',
                        'image' => $this->social_icon_url . '/02-grey/Instagram.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Instagram',
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
                'backgroundColor' => '#f6f6f6',
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
                    'type' => 'footer',
                    'text' => '<p><a href="[link:subscription_unsubscribe_url]">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]">' . __("Manage your subscription", 'mailpoet') . '</a></p>',
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
                        'fontColor' => '#41c7bf',
                        'textDecoration' => 'none',
                      ],
                    ],
                  ],
                  1 => [
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
          'fontSize' => '15px',
        ],
        'h1' => [
          'fontColor' => '#111111',
          'fontFamily' => 'Arial',
          'fontSize' => '30px',
        ],
        'h2' => [
          'fontColor' => '#222222',
          'fontFamily' => 'Arial',
          'fontSize' => '24px',
        ],
        'h3' => [
          'fontColor' => '#333333',
          'fontFamily' => 'Arial',
          'fontSize' => '22px',
        ],
        'link' => [
          'fontColor' => '#21759B',
          'textDecoration' => 'underline',
        ],
        'wrapper' => [
          'backgroundColor' => '#ffffff',
        ],
        'body' => [
          'backgroundColor' => '#f6f6f6',
        ],
      ],
    ];
  }

  private function getThumbnail() {
    return $this->template_image_url . '/thumbnail.20190411-1500.jpg';
  }
}
