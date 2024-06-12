<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class Faith {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
     $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/faith';
     $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Faith", 'mailpoet'),
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
                    'src' => $this->template_image_url . '/church-header.jpg',
                    'alt' => 'church-header',
                    'fullWidth' => true,
                    'width' => '1036px',
                    'height' => '563px',
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
                    'text' => '<h1 style="text-align: center;">Spreading Love &amp; Hope...</h1><p>Duis id molestie ex. Quisque finibus magna in justo tristique pellentesque. Nulla sed leo facilisis arcu malesuada molestie vel quis dolor. Donec imperdiet condimentum odio ut elementum. Aenean nisl massa, rutrum a ullamcorper eget, molestie non erat.&nbsp;</p>',
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
          1 => [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#f3f4f4',
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
                    'text' => '<h2 style="text-align: left;">Family Faith Events</h2>',
                  ],
                  2 => [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/family.jpg',
                    'alt' => 'family',
                    'fullWidth' => false,
                    'width' => '660px',
                    'height' => '880px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  3 => [
                    'type' => 'text',
                    'text' => '<p>In maximus tempus pellentesque. Nunc scelerisque ante odio, vel placerat dui fermentum efficitur. Integer vitae ex suscipit, aliquet eros vitae, ornare est. <a href="http://www.example.com">Aenean vel dapibus nisi</a>.</p>',
                  ],
                  4 => [
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
                    'type' => 'text',
                    'text' => '<h2>Thoughts &amp; Prayers</h2>',
                  ],
                  2 => [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/pray.jpg',
                    'alt' => 'pray',
                    'fullWidth' => false,
                    'width' => '660px',
                    'height' => '880px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  3 => [
                    'type' => 'text',
                    'text' => '<p>Donec sed vulputate ipsum. In scelerisque rutrum interdum. Donec imperdiet dignissim erat, in dictum lectus accumsan ut. <a href="http://www.example.com">Aliquam erat volutpat.</a></p>',
                  ],
                  4 => [
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
                    'text' => '<h1 style="text-align: center;">Latest News</h1>',
                  ],
                  2 => [
                    'type' => 'divider',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'padding' => '7px',
                        'borderStyle' => 'dotted',
                        'borderWidth' => '1px',
                        'borderColor' => '#dcdcdc',
                      ],
                    ],
                  ],
                  3 => [
                    'type' => 'automatedLatestContent',
                    'amount' => '3',
                    'contentType' => 'post',
                    'terms' => [],
                    'inclusionType' => 'include',
                    'displayType' => 'excerpt',
                    'titleFormat' => 'h3',
                    'titleAlignment' => 'left',
                    'titleIsLink' => false,
                    'imageFullWidth' => false,
                    'featuredImagePosition' => 'belowTitle',
                    'showAuthor' => 'no',
                    'authorPrecededBy' => 'Author:',
                    'showCategories' => 'no',
                    'categoriesPrecededBy' => 'Categories:',
                    'readMoreType' => 'button',
                    'readMoreText' => 'Read more',
                    'readMoreButton' => [
                      'type' => 'button',
                      'text' => 'Read more',
                      'url' => '[postLink]',
                      'styles' => [
                        'block' => [
                          'backgroundColor' => '#dfeaf3',
                          'borderColor' => '#00ddff',
                          'borderWidth' => '0px',
                          'borderRadius' => '5px',
                          'borderStyle' => 'solid',
                          'width' => '160px',
                          'lineHeight' => '45px',
                          'fontColor' => '#597890',
                          'fontFamily' => 'Tahoma',
                          'fontSize' => '16px',
                          'fontWeight' => 'normal',
                          'textAlign' => 'center',
                        ],
                      ],
                    ],
                    'sortBy' => 'newest',
                    'showDivider' => true,
                    'divider' => [
                      'type' => 'divider',
                      'styles' => [
                        'block' => [
                          'backgroundColor' => 'transparent',
                          'padding' => '13px',
                          'borderStyle' => 'dotted',
                          'borderWidth' => '2px',
                          'borderColor' => '#dfeaf3',
                        ],
                      ],
                    ],
                    'backgroundColor' => '#ffffff',
                    'backgroundColorAlternate' => '#eeeeee',
                  ],
                  4 => [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  5 => [
                    'type' => 'divider',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#e7eff6',
                        'padding' => '13px',
                        'borderStyle' => 'ridge',
                        'borderWidth' => '6px',
                        'borderColor' => '#597890',
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
                'backgroundColor' => '#e7eff6',
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
                    'text' => '<a href="[link:subscription_unsubscribe_url]">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]">' . __("Manage your subscription", 'mailpoet') . '</a><br /><b>' . __("Add your postal address here!", 'mailpoet') . '</b>',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#e7eff6',
                      ],
                      'text' => [
                        'fontColor' => '#787878',
                        'fontFamily' => 'Tahoma',
                        'fontSize' => '14px',
                        'textAlign' => 'left',
                      ],
                      'link' => [
                        'fontColor' => '#787878',
                        'textDecoration' => 'none',
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
                    'text' => '<p style="text-align: center;">Find us socially:</p>',
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
                        'iconType' => 'email',
                        'link' => '',
                        'image' => $this->social_icon_url . '/07-full-symbol-black/Email.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Email',
                      ],
                      3 => [
                        'type' => 'socialIcon',
                        'iconType' => 'website',
                        'link' => '',
                        'image' => $this->social_icon_url . '/07-full-symbol-black/Website.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Website',
                      ],
                    ],
                  ],
                  2 => [
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
        ],
      ],
      'globalStyles' => [
        'text' => [
          'fontColor' => '#787878',
          'fontFamily' => 'Tahoma',
          'fontSize' => '16px',
        ],
        'h1' => [
          'fontColor' => '#597890',
          'fontFamily' => 'Comic Sans MS',
          'fontSize' => '26px',
        ],
        'h2' => [
          'fontColor' => '#597890',
          'fontFamily' => 'Comic Sans MS',
          'fontSize' => '18px',
        ],
        'h3' => [
          'fontColor' => '#787878',
          'fontFamily' => 'Tahoma',
          'fontSize' => '18px',
        ],
        'link' => [
          'fontColor' => '#597890',
          'textDecoration' => 'underline',
        ],
        'wrapper' => [
          'backgroundColor' => '#ffffff',
        ],
        'body' => [
          'backgroundColor' => '#e7eff6',
        ],
      ],
    ];
  }

  private function getThumbnail() {
    return $this->template_image_url . '/thumbnail.20190411-1500.jpg';
  }
}
