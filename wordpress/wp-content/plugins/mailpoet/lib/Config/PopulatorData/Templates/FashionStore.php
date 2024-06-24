<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class FashionStore {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/fashion';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Fashion Store", 'mailpoet'),
      'categories' => json_encode(['standard', 'all']),
      'readonly' => 1,
      'thumbnail' => $this->getThumbnail(),
      'body' => json_encode($this->getBody()),
    ];
  }

  private function getThumbnail() {
    return $this->template_image_url . '/thumbnail.20190411-1500.jpeg';
  }

  private function getBody() {
    return
      [
        'content' =>
          [
            'type' => 'container',
            'orientation' => 'vertical',
            'image' =>
              [
                'src' => null,
                'display' => 'scale',
              ],
            'styles' =>
              [
                'block' =>
                  [
                    'backgroundColor' => 'transparent',
                  ],
              ],
            'blocks' =>
              [
                0 =>
                  [
                    'type' => 'container',
                    'orientation' => 'horizontal',
                    'image' =>
                      [
                        'src' => null,
                        'display' => 'scale',
                      ],
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => 'transparent',
                          ],
                      ],
                    'blocks' =>
                      [
                        0 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '20px',
                                          ],
                                      ],
                                  ],
                                1 =>
                                  [
                                    'type' => 'image',
                                    'link' => '',
                                    'src' => $this->template_image_url . '/tulip-2.png',
                                    'alt' => 'tulip',
                                    'fullWidth' => false,
                                    'width' => '26.5px',
                                    'height' => '64px',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'textAlign' => 'left',
                                          ],
                                      ],
                                  ],
                                2 =>
                                  [
                                    'type' => 'text',
                                    'text' => '<h1><strong>TULIP PARK</strong></h1>',
                                  ],
                              ],
                          ],
                        1 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '85px',
                                          ],
                                      ],
                                  ],
                                1 =>
                                  [
                                    'type' => 'text',
                                    'text' => '<h3 style="text-align: right;"><span style="color: #bcbcbc;">Since 1987</span></h3>',
                                  ],
                              ],
                          ],
                      ],
                  ],
                1 =>
                  [
                    'type' => 'container',
                    'orientation' => 'horizontal',
                    'image' =>
                      [
                        'src' => $this->template_image_url . '/Fashion-Header.jpg',
                        'display' => 'scale',
                      ],
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => '#f8f8f8',
                          ],
                      ],
                    'blocks' =>
                      [
                        0 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '486px',
                                          ],
                                      ],
                                  ],
                                1 =>
                                  [
                                    'type' => 'text',
                                    'text' => '<h1 style="text-align: left;"><span style="color: #ffffff;">Autumn/Winter</span></h1>',
                                  ],
                              ],
                          ],
                      ],
                  ],
                2 =>
                  [
                    'type' => 'container',
                    'orientation' => 'horizontal',
                    'image' =>
                      [
                        'src' => null,
                        'display' => 'scale',
                      ],
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => '#ffffff',
                          ],
                      ],
                    'blocks' =>
                      [
                        0 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '40px',
                                          ],
                                      ],
                                  ],
                                1 =>
                                  [
                                    'type' => 'text',
                                    'text' => '<h2 style="text-align: center;"><strong>The Autumn/Winter&nbsp;Range at Tulip Park</strong></h2>
<p style="text-align: center;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In elementum nunc vel est congue, a venenatis nunc aliquet. Curabitur luctus, nulla et dignissim elementum, ipsum eros fermentum nulla, non cursus eros mi eu velit. Nunc ex nibh, porta vulputate pharetra ac, placerat sed orci. Etiam enim enim, aliquet nec ligula in, ultrices iaculis dolor. Suspendisse potenti. Praesent fringilla augue ut lorem mattis, vitae fringilla nunc faucibus.</span></p>',
                                  ],
                                2 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '20px',
                                          ],
                                      ],
                                  ],
                              ],
                          ],
                      ],
                  ],
                3 =>
                  [
                    'type' => 'container',
                    'orientation' => 'horizontal',
                    'image' =>
                      [
                        'src' => null,
                        'display' => 'scale',
                      ],
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => 'transparent',
                          ],
                      ],
                    'blocks' =>
                      [
                        0 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'image',
                                    'link' => '',
                                    'src' => $this->template_image_url . '/Fashion-Items-1.jpg',
                                    'alt' => 'Fashion-Items-1',
                                    'fullWidth' => true,
                                    'width' => '364px',
                                    'height' => '291px',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'textAlign' => 'center',
                                          ],
                                      ],
                                  ],
                              ],
                          ],
                        1 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '36px',
                                          ],
                                      ],
                                  ],
                                1 =>
                                  [
                                    'type' => 'text',
                                    'text' => '<h3><strong>Title Goes Here</strong></h3>
<p><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In elementum nunc vel est congue, a venenatis nunc aliquet. Curabitur luctus, nulla et dignissim elementum, ipsum eros fermentum nulla, non cursus eros mi eu velit. Nunc ex nibh, porta vulputate pharetra ac, placerat sed orci.</span></p>',
                                  ],
                              ],
                          ],
                      ],
                  ],
                4 =>
                  [
                    'type' => 'container',
                    'orientation' => 'horizontal',
                    'image' =>
                      [
                        'src' => null,
                        'display' => 'scale',
                      ],
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => 'transparent',
                          ],
                      ],
                    'blocks' =>
                      [
                        0 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '36px',
                                          ],
                                      ],
                                  ],
                                1 =>
                                  [
                                    'type' => 'text',
                                    'text' => '<h3><strong>Title Goes Here</strong></h3>
<p><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In elementum nunc vel est congue, a venenatis nunc aliquet. Curabitur luctus, nulla et dignissim elementum, ipsum eros fermentum nulla, non cursus eros mi eu velit. Nunc ex nibh, porta vulputate pharetra ac, placerat sed orci.</span></p>',
                                  ],
                              ],
                          ],
                        1 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'image',
                                    'link' => '',
                                    'src' => $this->template_image_url . '/Fashion-Items-2.jpg',
                                    'alt' => 'Fashion-Items-2',
                                    'fullWidth' => true,
                                    'width' => '364px',
                                    'height' => '291px',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'textAlign' => 'center',
                                          ],
                                      ],
                                  ],
                              ],
                          ],
                      ],
                  ],
                5 =>
                  [
                    'type' => 'container',
                    'orientation' => 'horizontal',
                    'image' =>
                      [
                        'src' => null,
                        'display' => 'scale',
                      ],
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => 'transparent',
                          ],
                      ],
                    'blocks' =>
                      [
                        0 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'image',
                                    'link' => '',
                                    'src' => $this->template_image_url . '/Fashion-Items-3.jpg',
                                    'alt' => 'Fashion-Items-3',
                                    'fullWidth' => true,
                                    'width' => '364px',
                                    'height' => '291px',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'textAlign' => 'center',
                                          ],
                                      ],
                                  ],
                              ],
                          ],
                        1 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '36px',
                                          ],
                                      ],
                                  ],
                                1 =>
                                  [
                                    'type' => 'text',
                                    'text' => '<h3><strong>Title Goes Here</strong></h3>
<p><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In elementum nunc vel est congue, a venenatis nunc aliquet. Curabitur luctus, nulla et dignissim elementum, ipsum eros fermentum nulla, non cursus eros mi eu velit. Nunc ex nibh, porta vulputate pharetra ac, placerat sed orci.</span></p>',
                                  ],
                              ],
                          ],
                      ],
                  ],
                6 =>
                  [
                    'type' => 'container',
                    'orientation' => 'horizontal',
                    'image' =>
                      [
                        'src' => null,
                        'display' => 'scale',
                      ],
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => 'transparent',
                          ],
                      ],
                    'blocks' =>
                      [
                        0 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '35px',
                                          ],
                                      ],
                                  ],
                                1 =>
                                  [
                                    'type' => 'button',
                                    'text' => 'Check out the full range here',
                                    'url' => '',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => '#cdcdcd',
                                            'borderColor' => '#e4e4e4',
                                            'borderWidth' => '1px',
                                            'borderRadius' => '3px',
                                            'borderStyle' => 'solid',
                                            'width' => '288px',
                                            'lineHeight' => '40px',
                                            'fontColor' => '#000000',
                                            'fontFamily' => 'Arial',
                                            'fontSize' => '16px',
                                            'fontWeight' => 'bold',
                                            'textAlign' => 'center',
                                          ],
                                      ],
                                  ],
                                2 =>
                                  [
                                    'type' => 'divider',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'padding' => '13px',
                                            'borderStyle' => 'solid',
                                            'borderWidth' => '1px',
                                            'borderColor' => '#aaaaaa',
                                          ],
                                      ],
                                  ],
                                3 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '20px',
                                          ],
                                      ],
                                  ],
                              ],
                          ],
                      ],
                  ],
                7 =>
                  [
                    'type' => 'container',
                    'orientation' => 'horizontal',
                    'image' =>
                      [
                        'src' => null,
                        'display' => 'scale',
                      ],
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => 'transparent',
                          ],
                      ],
                    'blocks' =>
                      [
                        0 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'text',
                                    'text' => '<h2 style="text-align: center;"><strong>New in this week...</strong></h2>',
                                  ],
                              ],
                          ],
                      ],
                  ],
                8 =>
                  [
                    'type' => 'container',
                    'orientation' => 'horizontal',
                    'image' =>
                      [
                        'src' => null,
                        'display' => 'scale',
                      ],
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => 'transparent',
                          ],
                      ],
                    'blocks' =>
                      [
                        0 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'image',
                                    'link' => '',
                                    'src' => $this->template_image_url . '/Fashion-Items-4.jpg',
                                    'alt' => 'Fashion-Items-4',
                                    'fullWidth' => true,
                                    'width' => '364px',
                                    'height' => '291px',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'textAlign' => 'center',
                                          ],
                                      ],
                                  ],
                              ],
                          ],
                        1 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'image',
                                    'link' => '',
                                    'src' => $this->template_image_url . '/Fashion-Items-5.jpg',
                                    'alt' => 'Fashion-Items-5',
                                    'fullWidth' => true,
                                    'width' => '364px',
                                    'height' => '291px',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'textAlign' => 'center',
                                          ],
                                      ],
                                  ],
                              ],
                          ],
                        2 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'image',
                                    'link' => '',
                                    'src' => $this->template_image_url . '/Fashion-Items-6.jpg',
                                    'alt' => 'Fashion-Items-6',
                                    'fullWidth' => true,
                                    'width' => '364px',
                                    'height' => '291px',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'textAlign' => 'center',
                                          ],
                                      ],
                                  ],
                              ],
                          ],
                      ],
                  ],
                9 =>
                  [
                    'type' => 'container',
                    'orientation' => 'horizontal',
                    'image' =>
                      [
                        'src' => null,
                        'display' => 'scale',
                      ],
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => '#12223b',
                          ],
                      ],
                    'blocks' =>
                      [
                        0 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => '#f0f0f0',
                                            'height' => '20px',
                                          ],
                                      ],
                                  ],
                              ],
                          ],
                      ],
                  ],
                10 =>
                  [
                    'type' => 'container',
                    'orientation' => 'horizontal',
                    'image' =>
                      [
                        'src' => null,
                        'display' => 'scale',
                      ],
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => '#f0f0f0',
                          ],
                      ],
                    'blocks' =>
                      [
                        0 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '20px',
                                          ],
                                      ],
                                  ],
                                1 =>
                                  [
                                    'type' => 'text',
                                    'text' => '<h2 style="text-align: center;"><strong>TULIP PARK</strong></h2>',
                                  ],
                              ],
                          ],
                        1 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '20px',
                                          ],
                                      ],
                                  ],
                                1 =>
                                  [
                                    'type' => 'social',
                                    'iconSet' => 'full-symbol-black',
                                    'icons' =>
                                      [
                                        0 =>
                                          [
                                            'type' => 'socialIcon',
                                            'iconType' => 'facebook',
                                            'link' => 'http://www.facebook.com',
                                            'image' => $this->social_icon_url . '/07-full-symbol-black/Facebook.png',
                                            'height' => '32px',
                                            'width' => '32px',
                                            'text' => 'Facebook',
                                          ],
                                        1 =>
                                          [
                                            'type' => 'socialIcon',
                                            'iconType' => 'twitter',
                                            'link' => 'http://www.twitter.com',
                                            'image' => $this->social_icon_url . '/07-full-symbol-black/Twitter.png',
                                            'height' => '32px',
                                            'width' => '32px',
                                            'text' => 'Twitter',
                                          ],
                                        2 =>
                                          [
                                            'type' => 'socialIcon',
                                            'iconType' => 'instagram',
                                            'link' => 'http://instagram.com',
                                            'image' => $this->social_icon_url . '/07-full-symbol-black/Instagram.png',
                                            'height' => '32px',
                                            'width' => '32px',
                                            'text' => 'Instagram',
                                          ],
                                      ],
                                  ],
                              ],
                          ],
                        2 =>
                          [
                            'type' => 'container',
                            'orientation' => 'vertical',
                            'image' =>
                              [
                                'src' => null,
                                'display' => 'scale',
                              ],
                            'styles' =>
                              [
                                'block' =>
                                  [
                                    'backgroundColor' => 'transparent',
                                  ],
                              ],
                            'blocks' =>
                              [
                                0 =>
                                  [
                                    'type' => 'spacer',
                                    'styles' =>
                                      [
                                        'block' =>
                                          [
                                            'backgroundColor' => 'transparent',
                                            'height' => '20px',
                                          ],
                                      ],
                                  ],
                                1 =>
                                  [
                                    'type' => 'text',
                                    'text' => '<p style="font-size: 11px;"><span style="color: #000000;"><a href="[link:subscription_unsubscribe_url]" style="color: #000000;">' . __("Unsubscribe", 'mailpoet') . '</a>&nbsp;|&nbsp;<a href="[link:subscription_manage_url]" style="color: #000000;">' . __("Manage your subscription", 'mailpoet') . '</a></span><br /><span style="color: #000000;">' . __("Add your postal address here!", 'mailpoet') . '</span></p>',
                                  ],
                              ],
                          ],
                      ],
                  ],
              ],
          ],
        'globalStyles' =>
          [
            'text' =>
              [
                'fontColor' => '#000000',
                'fontFamily' => 'Arial',
                'fontSize' => '14px',
              ],
            'h1' =>
              [
                'fontColor' => '#111111',
                'fontFamily' => 'Courier New',
                'fontSize' => '30px',
              ],
            'h2' =>
              [
                'fontColor' => '#222222',
                'fontFamily' => 'Arial',
                'fontSize' => '24px',
              ],
            'h3' =>
              [
                'fontColor' => '#333333',
                'fontFamily' => 'Verdana',
                'fontSize' => '18px',
              ],
            'link' =>
              [
                'fontColor' => '#008282',
                'textDecoration' => 'underline',
              ],
            'wrapper' =>
              [
                'backgroundColor' => '#ffffff',
              ],
            'body' =>
              [
                'backgroundColor' => '#f0f0f0',
              ],
          ],
        'blockDefaults' =>
          [
            'automatedLatestContent' =>
              [
                'amount' => '5',
                'withLayout' => false,
                'contentType' => 'post',
                'inclusionType' => 'include',
                'displayType' => 'excerpt',
                'titleFormat' => 'h1',
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
                'readMoreButton' =>
                  [
                    'text' => 'Read more',
                    'url' => '[postLink]',
                    'context' => 'automatedLatestContent.readMoreButton',
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => '#2ea1cd',
                            'borderColor' => '#0074a2',
                            'borderWidth' => '1px',
                            'borderRadius' => '5px',
                            'borderStyle' => 'solid',
                            'width' => '180px',
                            'lineHeight' => '40px',
                            'fontColor' => '#ffffff',
                            'fontFamily' => 'Verdana',
                            'fontSize' => '18px',
                            'fontWeight' => 'normal',
                            'textAlign' => 'center',
                          ],
                      ],
                  ],
                'sortBy' => 'newest',
                'showDivider' => true,
                'divider' =>
                  [
                    'context' => 'automatedLatestContent.divider',
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => 'transparent',
                            'padding' => '13px',
                            'borderStyle' => 'solid',
                            'borderWidth' => '3px',
                            'borderColor' => '#aaaaaa',
                          ],
                      ],
                  ],
                'backgroundColor' => '#ffffff',
                'backgroundColorAlternate' => '#eeeeee',
              ],
            'automatedLatestContentLayout' =>
              [
                'amount' => '5',
                'withLayout' => true,
                'contentType' => 'post',
                'inclusionType' => 'include',
                'displayType' => 'excerpt',
                'titleFormat' => 'h1',
                'titleAlignment' => 'left',
                'titleIsLink' => false,
                'imageFullWidth' => false,
                'featuredImagePosition' => 'alternate',
                'showAuthor' => 'no',
                'authorPrecededBy' => 'Author:',
                'showCategories' => 'no',
                'categoriesPrecededBy' => 'Categories:',
                'readMoreType' => 'button',
                'readMoreText' => 'Read more',
                'readMoreButton' =>
                  [
                    'text' => 'Read more',
                    'url' => '[postLink]',
                    'context' => 'automatedLatestContentLayout.readMoreButton',
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => '#2ea1cd',
                            'borderColor' => '#0074a2',
                            'borderWidth' => '1px',
                            'borderRadius' => '5px',
                            'borderStyle' => 'solid',
                            'width' => '180px',
                            'lineHeight' => '40px',
                            'fontColor' => '#ffffff',
                            'fontFamily' => 'Verdana',
                            'fontSize' => '18px',
                            'fontWeight' => 'normal',
                            'textAlign' => 'center',
                          ],
                      ],
                  ],
                'sortBy' => 'newest',
                'showDivider' => true,
                'divider' =>
                  [
                    'context' => 'automatedLatestContentLayout.divider',
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => 'transparent',
                            'padding' => '13px',
                            'borderStyle' => 'solid',
                            'borderWidth' => '3px',
                            'borderColor' => '#aaaaaa',
                          ],
                      ],
                  ],
                'backgroundColor' => '#ffffff',
                'backgroundColorAlternate' => '#eeeeee',
              ],
            'button' =>
              [
                'text' => 'Button',
                'url' => '',
                'styles' =>
                  [
                    'block' =>
                      [
                        'backgroundColor' => '#2ea1cd',
                        'borderColor' => '#0074a2',
                        'borderWidth' => '1px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '180px',
                        'lineHeight' => '40px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Verdana',
                        'fontSize' => '18px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'center',
                      ],
                  ],
              ],
            'divider' =>
              [
                'styles' =>
                  [
                    'block' =>
                      [
                        'backgroundColor' => 'transparent',
                        'padding' => '13px',
                        'borderStyle' => 'solid',
                        'borderWidth' => '3px',
                        'borderColor' => '#aaaaaa',
                      ],
                  ],
              ],
            'footer' =>
              [
                'text' => '<p><a href="[link:subscription_unsubscribe_url]">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]">' . __("Manage your subscription", 'mailpoet') . '</a><br />' . __("Add your postal address here!", 'mailpoet') . '</p>',
                'styles' =>
                  [
                    'block' =>
                      [
                        'backgroundColor' => 'transparent',
                      ],
                    'text' =>
                      [
                        'fontColor' => '#222222',
                        'fontFamily' => 'Arial',
                        'fontSize' => '12px',
                        'textAlign' => 'center',
                      ],
                    'link' =>
                      [
                        'fontColor' => '#6cb7d4',
                        'textDecoration' => 'none',
                      ],
                  ],
              ],
            'posts' =>
              [
                'amount' => '10',
                'withLayout' => true,
                'contentType' => 'post',
                'postStatus' => 'publish',
                'inclusionType' => 'include',
                'displayType' => 'excerpt',
                'titleFormat' => 'h1',
                'titleAlignment' => 'left',
                'titleIsLink' => false,
                'imageFullWidth' => false,
                'featuredImagePosition' => 'alternate',
                'showAuthor' => 'no',
                'authorPrecededBy' => 'Author:',
                'showCategories' => 'no',
                'categoriesPrecededBy' => 'Categories:',
                'readMoreType' => 'link',
                'readMoreText' => 'Read more',
                'readMoreButton' =>
                  [
                    'text' => 'Read more',
                    'url' => '[postLink]',
                    'context' => 'posts.readMoreButton',
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => '#2ea1cd',
                            'borderColor' => '#0074a2',
                            'borderWidth' => '1px',
                            'borderRadius' => '5px',
                            'borderStyle' => 'solid',
                            'width' => '180px',
                            'lineHeight' => '40px',
                            'fontColor' => '#ffffff',
                            'fontFamily' => 'Verdana',
                            'fontSize' => '18px',
                            'fontWeight' => 'normal',
                            'textAlign' => 'center',
                          ],
                      ],
                  ],
                'sortBy' => 'newest',
                'showDivider' => true,
                'divider' =>
                  [
                    'context' => 'posts.divider',
                    'styles' =>
                      [
                        'block' =>
                          [
                            'backgroundColor' => 'transparent',
                            'padding' => '13px',
                            'borderStyle' => 'solid',
                            'borderWidth' => '3px',
                            'borderColor' => '#aaaaaa',
                          ],
                      ],
                  ],
                'backgroundColor' => '#ffffff',
                'backgroundColorAlternate' => '#eeeeee',
              ],
            'social' =>
              [
                'iconSet' => 'default',
                'icons' =>
                  [
                    0 =>
                      [
                        'type' => 'socialIcon',
                        'iconType' => 'facebook',
                        'link' => 'http://www.facebook.com',
                        'image' => $this->social_icon_url . '/01-social/Facebook.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Facebook',
                      ],
                    1 =>
                      [
                        'type' => 'socialIcon',
                        'iconType' => 'twitter',
                        'link' => 'http://www.twitter.com',
                        'image' => $this->social_icon_url . '/01-social/Twitter.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Twitter',
                      ],
                  ],
              ],
            'spacer' =>
              [
                'styles' =>
                  [
                    'block' =>
                      [
                        'backgroundColor' => 'transparent',
                        'height' => '486px',
                      ],
                  ],
                'type' => 'spacer',
              ],
            'header' =>
              [
                'text' => '<a href="[link:newsletter_view_in_browser_url]">' . __("View this in your browser.", 'mailpoet') . '</a>',
                'styles' =>
                  [
                    'block' =>
                      [
                        'backgroundColor' => 'transparent',
                      ],
                    'text' =>
                      [
                        'fontColor' => '#222222',
                        'fontFamily' => 'Arial',
                        'fontSize' => '12px',
                        'textAlign' => 'center',
                      ],
                    'link' =>
                      [
                        'fontColor' => '#6cb7d4',
                        'textDecoration' => 'underline',
                      ],
                  ],
              ],
          ],
      ];
  }
}
