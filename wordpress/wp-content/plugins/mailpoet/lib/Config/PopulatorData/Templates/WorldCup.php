<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class WorldCup {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
     $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/world_cup';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("World Cup", 'mailpoet'),
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
          [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#222222',
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
                        'height' => '20px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Sports-Football-Header.png',
                    'alt' => 'Sports-Football-Header',
                    'fullWidth' => true,
                    'width' => '1280px',
                    'height' => '220px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Sports-Football-Divider-1.png',
                    'alt' => 'Sports-Football-Divider-1',
                    'fullWidth' => true,
                    'width' => '1280px',
                    'height' => '50px',
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
                'backgroundColor' => '#da6110',
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
                        'height' => '20px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'text',
                    'text' => '<p><strong><span style="color: #ffffff; font-size: 14px;">Issue #1</span></strong></p>',
                  ],
                ],
              ],
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
                    'type' => 'text',
                    'text' => '<p style="text-align: right;"><a href="[link:newsletter_view_in_browser_url]" target="_blank" style="color: #ffffff; font-size: 14px; text-align: center;">View In Browser</a></p>
                                        <p style="text-align: right;"><span style="color: #ffffff; text-align: start;">Monday 1st January 2017</span></p>',
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
                        'backgroundColor' => '#da6110',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Sports-Football-Header-1.png',
                    'alt' => 'Sports-Football-Header',
                    'fullWidth' => true,
                    'width' => '1280px',
                    'height' => '580px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
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
                    'type' => 'text',
                    'text' => '<h2 style="text-align: left;"><strong>Welcome Back!</strong></h2>
                                      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam consequat lorem at est congue, non consequat lacus iaculis. Integer euismod mauris velit, vel ultrices nibh bibendum quis. Donec eget fermentum magna.</p>
                                      <p></p>
                                      <p>Nullam congue dui lectus, quis pellentesque orci placerat eu. Fusce semper neque a mi aliquet vulputate sed sit amet nisi. Etiam sed nisl nec orci pretium lacinia eget in turpis. Maecenas in posuere justo. Vestibulum et sapien vestibulum, imperdiet neque in, maximus velit.</p>
                                      <p></p>
                                      <p>Proin dignissim elit magna, viverra scelerisque libero vehicula sed</p>',
                  ],
                  [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Sports-Football-Divider-3.png',
                    'alt' => 'Sports-Football-Divider-3',
                    'fullWidth' => true,
                    'width' => '1280px',
                    'height' => '50px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#efefef',
                        'height' => '20px',
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
                'backgroundColor' => '#efefef',
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
                    'type' => 'text',
                    'text' => '<h2 style="padding-bottom: 0;"><span style="font-weight: 600;">Latest News</span></h2>',
                  ],
                ],
              ],
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
                    'type' => 'button',
                    'text' => 'View All News',
                    'url' => '',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#d35400',
                        'borderColor' => '#d35400',
                        'borderWidth' => '1px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '110px',
                        'lineHeight' => '36px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Tahoma',
                        'fontSize' => '14px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'right',
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
                'backgroundColor' => '#efefef',
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
                        'height' => '20px',
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
                'backgroundColor' => '#efefef',
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
                    'link' => 'http://mailpoet.info/brazils-history-making-hurricane/',
                    'src' => $this->template_image_url . '/2865897_full-lnd.jpg',
                    'alt' => 'Brazil’s history-making Hurricane',
                    'fullWidth' => false,
                    'width' => 652,
                    'height' => 366,
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
                    'type' => 'text',
                    'text' => '<h3 style="text-align: left;"><strong>Brazil&rsquo;s history-making Hurricane</strong></h3>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam consequat lorem at est congue, non consequat lacus iaculis. Integer euismod mauris velit, vel ultrices nibh bibendum quis. Donec eget fermentum magna. Nullam congue dui lectus, quis pellentesque orci placerat eu. Fusce semper neque a mi aliquet vulputate sed sit amet nisi...</p>
                                            <p><a href="http://mailpoet.info/brazils-history-making-hurricane/">Read More</a></p>',
                  ],
                  [
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
          [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#efefef',
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
                    'link' => 'http://mailpoet.info/icelands-dentist-coach-defying-convention-and-expectations/',
                    'src' => $this->template_image_url . '/2866107_full-lnd.jpg',
                    'alt' => 'Iceland’s dentist-coach defying convention and expectations',
                    'fullWidth' => false,
                    'width' => 652,
                    'height' => 366,
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
                    'type' => 'text',
                    'text' => '<h3><strong>Iceland&rsquo;s dentist-coach defying convention and expectations</strong></h3>
                                          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam consequat lorem at est congue, non consequat lacus iaculis. Integer euismod mauris velit...</p>
                                          <p><a href="http://mailpoet.info/icelands-dentist-coach-defying-convention-and-expectations/">Read More</a></p>',
                  ],
                ],
              ],
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
                    'link' => 'http://mailpoet.info/impact-and-legacy-of-2018-fifa-world-cup-russia-facts-and-figures/',
                    'src' => $this->template_image_url . '/2709222_full-lnd.jpg',
                    'alt' => 'Impact and legacy of 2018 FIFA World Cup Russia: facts and figures',
                    'fullWidth' => false,
                    'width' => 652,
                    'height' => 366,
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
                    'type' => 'text',
                    'text' => '<h3 style="text-align: left;"><strong>Impact and legacy of 2018 FIFA World Cup Russia: facts and figures</strong></h3>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam consequat lorem at est congue, non consequat lacus iaculis. Integer euismod...</p>
                                        <p><a href="http://mailpoet.info/impact-and-legacy-of-2018-fifa-world-cup-russia-facts-and-figures/">Read More</a></p>',
                  ],
                ],
              ],
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
                    'link' => 'http://mailpoet.info/linekers-life-changing-treble/',
                    'src' => $this->template_image_url . '/2867790_full-lnd.jpg',
                    'alt' => 'Lineker’s life-changing treble',
                    'fullWidth' => false,
                    'width' => 652,
                    'height' => 366,
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],

                  [
                    'type' => 'text',
                    'text' => '<h3 style="text-align: left;"><strong>Lineker&rsquo;s life-changing treble</strong></h3>
                                      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam consequat lorem at est congue, non consequat lacus iaculis. Integer euismod mauris velit&nbsp;<span style="background-color: inherit;">consequat lorem at est congue...</span></p>
                                      <p><a href="http://mailpoet.info/linekers-life-changing-treble/">Read More</a></p>',
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
                'backgroundColor' => '#f8f8f8',
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
                        'backgroundColor' => '#efefef',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Sports-Football-Divider-2.png',
                    'alt' => 'Sports-Football-Divider-2',
                    'fullWidth' => true,
                    'width' => '1280px',
                    'height' => '50px',
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
                'backgroundColor' => '#222222',
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
                    'src' => $this->template_image_url . '/Sports-Football-Footer-1.png',
                    'alt' => 'Sports-Football-Footer',
                    'fullWidth' => true,
                    'width' => '1280px',
                    'height' => '500px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => '#da6110',
                        'height' => '20px',
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
                'backgroundColor' => '#da6110',
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
                    'type' => 'social',
                    'iconSet' => 'full-symbol-grey',
                    'icons' => [
                      [
                        'type' => 'socialIcon',
                        'iconType' => 'facebook',
                        'link' => 'http://www.facebook.com',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Facebook.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Facebook',
                      ],
                      [
                        'type' => 'socialIcon',
                        'iconType' => 'twitter',
                        'link' => 'http://www.twitter.com',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Twitter.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Twitter',
                      ],
                      [
                        'type' => 'socialIcon',
                        'iconType' => 'youtube',
                        'link' => 'http://www.youtube.com',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Youtube.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Youtube',
                      ],
                      [
                        'type' => 'socialIcon',
                        'iconType' => 'instagram',
                        'link' => 'http://instagram.com',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Instagram.png',
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
          [
            'type' => 'container',
            'orientation' => 'horizontal',
            'styles' => [
              'block' => [
                'backgroundColor' => '#b55311',
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
                        'backgroundColor' => '#da6110',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Sports-Football-Logo-Small.png',
                    'alt' => 'Sports-Football-Logo-Small',
                    'fullWidth' => false,
                    'width' => '772px',
                    'height' => '171px',
                    'styles' => [
                      'block' => [
                        'textAlign' => 'center',
                      ],
                    ],
                  ],
                  [
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
                        'backgroundColor' => '#da6110',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'spacer',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                        'height' => '20px',
                      ],
                    ],
                  ],
                  [
                    'type' => 'footer',
                    'text' => '<p><a href="[link:subscription_unsubscribe_url]">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]">' . __("Manage your subscription", 'mailpoet') . '</a><br />' . __("Add your postal address here!", 'mailpoet') . '</p>',
                    'styles' => [
                      'block' => [
                        'backgroundColor' => 'transparent',
                      ],
                      'text' => [
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Tahoma',
                        'fontSize' => '12px',
                        'textAlign' => 'right',
                      ],
                      'link' => [
                        'fontColor' => '#ffffff',
                        'textDecoration' => 'underline',
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
          'fontFamily' => 'Tahoma',
          'fontSize' => '14px',
        ],
        'h1' => [
          'fontColor' => '#111111',
          'fontFamily' => 'Tahoma',
          'fontSize' => '30px',
        ],
        'h2' => [
          'fontColor' => '#da6110',
          'fontFamily' => 'Tahoma',
          'fontSize' => '24px',
        ],
        'h3' => [
          'fontColor' => '#333333',
          'fontFamily' => 'Tahoma',
          'fontSize' => '18px',
        ],
        'link' => [
          'fontColor' => '#da6110',
          'textDecoration' => 'underline',
        ],
        'wrapper' => [
          'backgroundColor' => '#ffffff',
        ],
        'body' => [
          'backgroundColor' => '#222222',
        ],
      ],
    ];
  }

  private function getThumbnail() {
    return $this->template_image_url . '/thumbnail.20190411-1500.jpg';
  }
}
