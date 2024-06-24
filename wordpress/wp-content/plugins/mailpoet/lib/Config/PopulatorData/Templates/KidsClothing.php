<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class KidsClothing {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/kids-clothing';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Abandoned Cart – Kids", 'mailpoet'),
      'categories' => json_encode(['woocommerce', 'all']),
      'readonly' => 1,
      'thumbnail' => $this->getThumbnail(),
      'body' => json_encode($this->getBody()),
    ];
  }

  private function getThumbnail() {
    return $this->template_image_url . '/thumbnail.20201028.jpg';
  }

  private function getBody() {
    return [
      'content' => [
          'type' => 'container',
          'columnLayout' => false,
          'orientation' => 'vertical',
          'image' => [
              'display' => 'scale',
              'src' => null,
            ],
          'styles' => [
              'block' =>
                 [
                  'backgroundColor' => 'transparent',
                ],
            ],
          'blocks' => [
              0 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'display' => 'scale',
                      'src' => null,
                    ],
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#c3e1e8',
                        ],
                    ],
                  'blocks' =>
                     [
                      0 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                            ],
                        ],
                    ],
                ],
              1 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'display' => 'scale',
                      'src' => null,
                    ],
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#c3e1e8',
                        ],
                    ],
                  'blocks' =>
                     [
                      0 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'type' => 'social',
                                  'iconSet' => 'circles',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'textAlign' => 'center',
                                        ],
                                    ],
                                  'icons' =>
                                     [
                                      0 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'facebook',
                                          'link' => 'http://www.facebook.com',
                                          'image' => $this->social_icon_url . '/03-circles/Facebook.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Facebook',
                                        ],
                                      1 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'twitter',
                                          'link' => 'http://www.twitter.com',
                                          'image' => $this->social_icon_url . '/03-circles/Twitter.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Twitter',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                      1 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'src' => $this->template_image_url . '/Kids-Clothing-Logo.png',
                                  'alt' => 'Kids-Clothing-Logo',
                                  'fullWidth' => true,
                                  'width' => '250px',
                                  'height' => '121px',
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
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'type' => 'social',
                                  'iconSet' => 'circles',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'textAlign' => 'center',
                                        ],
                                    ],
                                  'icons' =>
                                     [
                                      0 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'google-plus',
                                          'link' => 'http://plus.google.com',
                                          'image' => $this->social_icon_url . '/03-circles/Google-Plus.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Google Plus',
                                        ],
                                      1 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'instagram',
                                          'link' => 'http://instagram.com',
                                          'image' => $this->social_icon_url . '/03-circles/Instagram.png',
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
              2 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'display' => 'scale',
                      'src' => null,
                    ],
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#9bd2e0',
                        ],
                    ],
                  'blocks' =>
                     [
                      0 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                            ],
                        ],
                    ],
                ],
              3 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'display' => 'scale',
                      'src' => null,
                    ],
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#9bd2e0',
                        ],
                    ],
                  'blocks' =>
                     [
                      0 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'text' => '<p style="text-align: center;"><span style="color: #4e4e4e;"><strong>' . __("Boys Clothes", 'mailpoet') . '</strong></span></p>',
                                ],
                            ],
                        ],
                      1 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'text' => '<p style="text-align: center;"><span style="color: #4e4e4e;"><strong>' . __("Girls Clothes", 'mailpoet') . '</strong></span></p>',
                                ],
                            ],
                        ],
                      2 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'text' => '<p style="text-align: center;"><span style="color: #4e4e4e;"><strong>' . __("Toys & Games", 'mailpoet') . '</strong></span></p>',
                                ],
                            ],
                        ],
                    ],
                ],
              4 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'src' => $this->template_image_url . '/Kids-Clothing-Header.jpg',
                      'display' => 'scale',
                    ],
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#9cd1e1',
                        ],
                    ],
                  'blocks' =>
                     [
                      0 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
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
                                          'height' => '80px',
                                        ],
                                    ],
                                ],
                              1 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<h1><strong>' . __("Wait!", 'mailpoet') . '</strong></h1><h3>' . __("You’ve left something in your cart!", 'mailpoet') . '</h3>',
                                ],
                              2 =>
                                 [
                                  'type' => 'spacer',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => 'transparent',
                                          'height' => '100px',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                      1 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
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
                            ],
                        ],
                    ],
                ],
              5 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'display' => 'scale',
                      'src' => null,
                    ],
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#9bd2e0',
                        ],
                    ],
                  'blocks' =>
                     [
                      0 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'text' => '<h2 style="text-align: center;"><span style="color: #4e4e4e;"><strong>' . __("Don’t worry, we saved it for you…", 'mailpoet') . '</strong></span></h2>',
                                ],
                            ],
                        ],
                    ],
                ],
              6 =>
                 [
                  'type' => 'abandonedCartContent',
                  'withLayout' => true,
                  'amount' => '2',
                  'contentType' => 'product',
                  'postStatus' => 'publish',
                  'inclusionType' => 'include',
                  'displayType' => 'excerpt',
                  'titleFormat' => 'h2',
                  'titleAlignment' => 'left',
                  'titleIsLink' => false,
                  'imageFullWidth' => false,
                  'titlePosition' => 'aboveExcerpt',
                  'featuredImagePosition' => 'left',
                  'pricePosition' => 'below',
                  'readMoreType' => 'none',
                  'readMoreText' => '',
                  'readMoreButton' =>
                     [
                    ],
                  'sortBy' => 'newest',
                  'showDivider' => true,
                  'divider' =>
                     [
                      'type' => 'divider',
                      'styles' =>
                         [
                          'block' =>
                             [
                              'backgroundColor' => 'transparent',
                              'borderColor' => '#aaaaaa',
                              'borderStyle' => 'solid',
                              'borderWidth' => '3px',
                              'padding' => '13px',
                            ],
                        ],
                      'context' => 'abandonedCartContent.divider',
                    ],
                  'backgroundColor' => '#ffffff',
                  'backgroundColorAlternate' => '#eeeeee',
                ],
              7 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'display' => 'scale',
                      'src' => null,
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
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'type' => 'button',
                                  'text' => __('Go To Cart', 'mailpoet'),
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#9bd2e0',
                                          'borderColor' => '#0074a2',
                                          'borderRadius' => '40px',
                                          'borderStyle' => 'solid',
                                          'borderWidth' => '0px',
                                          'fontColor' => '#4e4e4e',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '18px',
                                          'fontWeight' => 'bold',
                                          'lineHeight' => '40px',
                                          'textAlign' => 'center',
                                          'width' => '154px',
                                        ],
                                    ],
                                ],
                              1 =>
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
              8 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'display' => 'scale',
                      'src' => null,
                    ],
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#fceba5',
                        ],
                    ],
                  'blocks' =>
                     [
                      0 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'text' => '<h2 style="text-align: center;"><strong>' . __("YOU MIGHT ALSO LIKE…", 'mailpoet') . '</strong></h2>',
                                ],
                            ],
                        ],
                    ],
                ],
              9 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'display' => 'scale',
                      'src' => null,
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
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                            ],
                        ],
                    ],
                ],
              10 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'display' => 'scale',
                      'src' => null,
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
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'src' => $this->template_image_url . '/Kids-Clothing-Image-3.jpg',
                                  'alt' => 'Kids-Clothing-Image-3',
                                  'fullWidth' => false,
                                  'width' => '500px',
                                  'height' => '500px',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'textAlign' => 'center',
                                        ],
                                    ],
                                ],
                              1 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<p><strong>' . __("Cherry Dress", 'mailpoet') . '</strong></p><p><span>$10.99</span></p>',
                                ],
                              2 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'View',
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#9bd2e0',
                                          'borderColor' => '#0074a2',
                                          'borderRadius' => '40px',
                                          'borderStyle' => 'solid',
                                          'borderWidth' => '0px',
                                          'fontColor' => '#4e4e4e',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '18px',
                                          'fontWeight' => 'bold',
                                          'lineHeight' => '40px',
                                          'textAlign' => 'left',
                                          'width' => '90px',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                      1 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'src' => $this->template_image_url . '/Kids-Clothing-Image-2.jpg',
                                  'alt' => 'Kids-Clothing-Image-2',
                                  'fullWidth' => false,
                                  'width' => '500px',
                                  'height' => '500px',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'textAlign' => 'center',
                                        ],
                                    ],
                                ],
                              1 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<p><strong>' . __("Red T-Shirt", 'mailpoet') . '</strong></p><p><span>$9.49</span></p>',
                                ],
                              2 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'View',
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#9bd2e0',
                                          'borderColor' => '#0074a2',
                                          'borderRadius' => '40px',
                                          'borderStyle' => 'solid',
                                          'borderWidth' => '0px',
                                          'fontColor' => '#4e4e4e',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '18px',
                                          'fontWeight' => 'bold',
                                          'lineHeight' => '40px',
                                          'textAlign' => 'left',
                                          'width' => '90px',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                      2 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'src' => $this->template_image_url . '/Kids-Clothing-Image-4.jpg',
                                  'alt' => 'Kids-Clothing-Image-4',
                                  'fullWidth' => false,
                                  'width' => '500px',
                                  'height' => '500px',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'textAlign' => 'center',
                                        ],
                                    ],
                                ],
                              1 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<p><strong>' . __("Pink Dance Dress", 'mailpoet') . '</strong></p><p><span>$11.99</span></p>',
                                ],
                              2 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'View',
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#9bd2e0',
                                          'borderColor' => '#0074a2',
                                          'borderRadius' => '40px',
                                          'borderStyle' => 'solid',
                                          'borderWidth' => '0px',
                                          'fontColor' => '#4e4e4e',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '18px',
                                          'fontWeight' => 'bold',
                                          'lineHeight' => '40px',
                                          'textAlign' => 'left',
                                          'width' => '90px',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
              11 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'display' => 'scale',
                      'src' => null,
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
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'src' => $this->template_image_url . '/Kids-Clothing-Footer.jpg',
                                  'alt' => 'Kids-Clothing-Footer',
                                  'fullWidth' => true,
                                  'width' => '1280px',
                                  'height' => '107px',
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
              12 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
                  'orientation' => 'horizontal',
                  'image' =>
                     [
                      'display' => 'scale',
                      'src' => null,
                    ],
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#c3e1e8',
                        ],
                    ],
                  'blocks' =>
                     [
                      0 =>
                         [
                          'type' => 'container',
                          'columnLayout' => false,
                          'orientation' => 'vertical',
                          'image' =>
                             [
                              'display' => 'scale',
                              'src' => null,
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
                                  'src' => $this->template_image_url . '/Kids-Clothing-Logo-Footer-150x61.png',
                                  'alt' => 'Kids-Clothing-Logo-Footer',
                                  'fullWidth' => false,
                                  'width' => '150px',
                                  'height' => '61px',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'textAlign' => 'center',
                                        ],
                                    ],
                                ],
                              2 =>
                                 [
                                  'type' => 'footer',
                                  'text' => '<p><strong><span style="color: #333333;"><a href="[link:subscription_unsubscribe_url]" style="color: #333333;">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]" style="color: #333333;">' . __("Manage your subscription", 'mailpoet') . '</a></span></strong><br />' . __("Add your postal address here!", 'mailpoet') . '</p>',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => 'transparent',
                                        ],
                                      'link' =>
                                         [
                                          'fontColor' => '#6cb7d4',
                                          'textDecoration' => 'none',
                                        ],
                                      'text' =>
                                         [
                                          'fontColor' => '#222222',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '12px',
                                          'textAlign' => 'center',
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
              'fontColor' => '#4e4e4e',
              'fontFamily' => 'Arial',
              'fontSize' => '16px',
              'lineHeight' => '1.6',
            ],
          'h1' => [
              'fontColor' => '#4e4e4e',
              'fontFamily' => 'Arial',
              'fontSize' => '40px',
              'lineHeight' => '1.6',
            ],
          'h2' => [
              'fontColor' => '#4e4e4e',
              'fontFamily' => 'Arial',
              'fontSize' => '24px',
              'lineHeight' => '1.6',
            ],
          'h3' => [
              'fontColor' => '#4e4e4e',
              'fontFamily' => 'Arial',
              'fontSize' => '26px',
              'lineHeight' => '1.6',
            ],
          'link' => [
              'fontColor' => '#9bd2e0',
              'textDecoration' => 'underline',
            ],
          'wrapper' => [
              'backgroundColor' => '#ffffff',
            ],
          'body' => [
              'backgroundColor' => '#c3e1e8',
            ],
        ],
      'blockDefaults' => [
          'abandonedCartContent' => [
              'amount' => '2',
              'withLayout' => true,
              'contentType' => 'product',
              'postStatus' => 'publish',
              'inclusionType' => 'include',
              'displayType' => 'excerpt',
              'titleFormat' => 'h2',
              'titleAlignment' => 'left',
              'titleIsLink' => false,
              'imageFullWidth' => false,
              'featuredImagePosition' => 'left',
              'pricePosition' => 'below',
              'readMoreType' => 'none',
              'readMoreText' => '',
              'readMoreButton' =>
                 [
                ],
              'sortBy' => 'newest',
              'showDivider' => true,
              'divider' =>
                 [
                  'context' => 'abandonedCartContent.divider',
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
                  'type' => 'divider',
                ],
              'backgroundColor' => '#ffffff',
              'backgroundColorAlternate' => '#eeeeee',
              'type' => 'abandonedCartContent',
              'titlePosition' => 'aboveExcerpt',
            ],
          'automatedLatestContent' => [
              'amount' => '5',
              'authorPrecededBy' => __('Author:', 'mailpoet'),
              'backgroundColor' => '#ffffff',
              'backgroundColorAlternate' => '#eeeeee',
              'categoriesPrecededBy' => __('Categories:', 'mailpoet'),
              'contentType' => 'post',
              'displayType' => 'excerpt',
              'divider' =>
                 [
                  'context' => 'automatedLatestContent.divider',
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => 'transparent',
                          'borderColor' => '#aaaaaa',
                          'borderStyle' => 'solid',
                          'borderWidth' => '3px',
                          'padding' => '13px',
                        ],
                    ],
                ],
              'featuredImagePosition' => 'belowTitle',
              'imageFullWidth' => false,
              'inclusionType' => 'include',
              'readMoreButton' =>
                 [
                  'context' => 'automatedLatestContent.readMoreButton',
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#2ea1cd',
                          'borderColor' => '#0074a2',
                          'borderRadius' => '5px',
                          'borderStyle' => 'solid',
                          'borderWidth' => '1px',
                          'fontColor' => '#ffffff',
                          'fontFamily' => 'Verdana',
                          'fontSize' => '18px',
                          'fontWeight' => 'normal',
                          'lineHeight' => '40px',
                          'textAlign' => 'center',
                          'width' => '180px',
                        ],
                    ],
                  'text' => __('Read more', 'mailpoet'),
                  'url' => '[postLink]',
                ],
              'readMoreText' => __('Read more', 'mailpoet'),
              'readMoreType' => 'button',
              'showAuthor' => 'no',
              'showCategories' => 'no',
              'showDivider' => true,
              'sortBy' => 'newest',
              'titleAlignment' => 'left',
              'titleFormat' => 'h1',
              'titleIsLink' => false,
            ],
          'automatedLatestContentLayout' => [
              'amount' => '5',
              'authorPrecededBy' => __('Author:', 'mailpoet'),
              'backgroundColor' => '#ffffff',
              'backgroundColorAlternate' => '#eeeeee',
              'categoriesPrecededBy' => 'Categories:',
              'contentType' => 'post',
              'displayType' => 'excerpt',
              'divider' =>
                 [
                  'context' => 'automatedLatestContentLayout.divider',
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => 'transparent',
                          'borderColor' => '#aaaaaa',
                          'borderStyle' => 'solid',
                          'borderWidth' => '3px',
                          'padding' => '13px',
                        ],
                    ],
                ],
              'featuredImagePosition' => 'alternate',
              'imageFullWidth' => false,
              'inclusionType' => 'include',
              'readMoreButton' =>
                 [
                  'context' => 'automatedLatestContentLayout.readMoreButton',
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#2ea1cd',
                          'borderColor' => '#0074a2',
                          'borderRadius' => '5px',
                          'borderStyle' => 'solid',
                          'borderWidth' => '1px',
                          'fontColor' => '#ffffff',
                          'fontFamily' => 'Verdana',
                          'fontSize' => '18px',
                          'fontWeight' => 'normal',
                          'lineHeight' => '40px',
                          'textAlign' => 'center',
                          'width' => '180px',
                        ],
                    ],
                  'text' => __('Read more', 'mailpoet'),
                  'url' => '[postLink]',
                ],
              'readMoreText' => __('Read more', 'mailpoet'),
              'readMoreType' => 'button',
              'showAuthor' => 'no',
              'showCategories' => 'no',
              'showDivider' => true,
              'sortBy' => 'newest',
              'titleAlignment' => 'left',
              'titleFormat' => 'h1',
              'titleIsLink' => false,
              'withLayout' => true,
            ],
          'button' => [
              'styles' =>
                 [
                  'block' =>
                     [
                      'backgroundColor' => '#9bd2e0',
                      'borderColor' => '#0074a2',
                      'borderRadius' => '40px',
                      'borderStyle' => 'solid',
                      'borderWidth' => '0px',
                      'fontColor' => '#4e4e4e',
                      'fontFamily' => 'Arial',
                      'fontSize' => '18px',
                      'fontWeight' => 'bold',
                      'lineHeight' => '40px',
                      'textAlign' => 'center',
                      'width' => '154px',
                    ],
                ],
              'text' => 'Go To Cart',
              'url' => '',
              'type' => 'button',
            ],
          'container' => [
            ],
          'divider' => [
              'styles' =>
                 [
                  'block' =>
                     [
                      'backgroundColor' => 'transparent',
                      'borderColor' => '#aaaaaa',
                      'borderStyle' => 'solid',
                      'borderWidth' => '3px',
                      'padding' => '13px',
                    ],
                ],
            ],
          'footer' => [
              'styles' =>
                 [
                  'block' =>
                     [
                      'backgroundColor' => 'transparent',
                    ],
                  'link' =>
                     [
                      'fontColor' => '#6cb7d4',
                      'textDecoration' => 'none',
                    ],
                  'text' =>
                     [
                      'fontColor' => '#222222',
                      'fontFamily' => 'Arial',
                      'fontSize' => '12px',
                      'textAlign' => 'center',
                    ],
                ],
              'text' => '<p><a href="[link:subscription_unsubscribe_url]">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]">' . __("Manage your subscription", 'mailpoet') . '</a><br />' . __("Add your postal address here!", 'mailpoet') . '</p>',
            ],
          'posts' => [
              'amount' => '10',
              'authorPrecededBy' => __('Author:', 'mailpoet'),
              'backgroundColor' => '#ffffff',
              'backgroundColorAlternate' => '#eeeeee',
              'categoriesPrecededBy' => 'Categories:',
              'contentType' => 'post',
              'displayType' => 'excerpt',
              'divider' =>
                 [
                  'context' => 'posts.divider',
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => 'transparent',
                          'borderColor' => '#aaaaaa',
                          'borderStyle' => 'solid',
                          'borderWidth' => '3px',
                          'padding' => '13px',
                        ],
                    ],
                ],
              'featuredImagePosition' => 'belowTitle',
              'imageFullWidth' => false,
              'inclusionType' => 'include',
              'postStatus' => 'publish',
              'readMoreButton' =>
                 [
                  'context' => 'posts.readMoreButton',
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#2ea1cd',
                          'borderColor' => '#0074a2',
                          'borderRadius' => '5px',
                          'borderStyle' => 'solid',
                          'borderWidth' => '1px',
                          'fontColor' => '#ffffff',
                          'fontFamily' => 'Verdana',
                          'fontSize' => '18px',
                          'fontWeight' => 'normal',
                          'lineHeight' => '40px',
                          'textAlign' => 'center',
                          'width' => '180px',
                        ],
                    ],
                  'text' => __('Read more', 'mailpoet'),
                  'url' => '[postLink]',
                ],
              'readMoreText' => __('Read more', 'mailpoet'),
              'readMoreType' => 'link',
              'showAuthor' => 'no',
              'showCategories' => 'no',
              'showDivider' => true,
              'sortBy' => 'newest',
              'titleAlignment' => 'left',
              'titleFormat' => 'h1',
              'titleIsLink' => false,
            ],
          'products' => [
              'amount' => '10',
              'withLayout' => true,
              'contentType' => 'product',
              'postStatus' => 'publish',
              'inclusionType' => 'include',
              'displayType' => 'excerpt',
              'titleFormat' => 'h1',
              'titleAlignment' => 'left',
              'titleIsLink' => false,
              'imageFullWidth' => false,
              'featuredImagePosition' => 'alternate',
              'pricePosition' => 'below',
              'readMoreType' => 'link',
              'readMoreText' => __('Buy now', 'mailpoet'),
              'readMoreButton' =>
                 [
                  'text' => __('Buy now', 'mailpoet'),
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
          'social' => [
              'iconSet' => 'default',
              'icons' =>
                 [
                  0 =>
                     [
                      'height' => '32px',
                      'iconType' => 'facebook',
                      'image' => $this->social_icon_url . '/01-social/Facebook.png',
                      'link' => 'http://www.facebook.com',
                      'text' => 'Facebook',
                      'type' => 'socialIcon',
                      'width' => '32px',
                     ],
                  1 =>
                     [
                      'height' => '32px',
                      'iconType' => 'twitter',
                      'image' => $this->social_icon_url . '/01-social/Twitter.png',
                      'link' => 'http://www.twitter.com',
                      'text' => 'Twitter',
                      'type' => 'socialIcon',
                      'width' => '32px',
                     ],
                ],
            ],
          'spacer' => [
              'styles' =>
                 [
                  'block' =>
                     [
                      'backgroundColor' => 'transparent',
                      'height' => '80px',
                    ],
                ],
              'type' => 'spacer',
            ],
          'header' => [
              'styles' =>
                 [
                  'block' =>
                     [
                      'backgroundColor' => 'transparent',
                    ],
                  'link' =>
                     [
                      'fontColor' => '#6cb7d4',
                      'textDecoration' => 'underline',
                    ],
                  'text' =>
                     [
                      'fontColor' => '#222222',
                      'fontFamily' => 'Arial',
                      'fontSize' => '12px',
                      'textAlign' => 'center',
                    ],
                ],
              'text' => '<a href="[link:newsletter_view_in_browser_url]">' . __("View this in your browser.", 'mailpoet') . '</a>',
            ],
          'woocommerceHeading' => [
              'contents' =>
                 [
                  'new_account' => __('New Order: #0001', 'mailpoet'),
                  'processing_order' => __('Thank you for your order', 'mailpoet'),
                  'completed_order' => __('Thanks for shopping with us', 'mailpoet'),
                  'customer_note' => __('A note has been added to your order', 'mailpoet'),
                ],
            ],
        ],
    ];
  }
}
