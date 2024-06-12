<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class Fitness {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/fitness';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Abandoned Cart – Fitness", 'mailpoet'),
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
      'content' =>
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
                  'type' => 'container',
                  'columnLayout' => false,
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
                          'backgroundColor' => '#e6e1e5',
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
                                  'type' => 'image',
                                  'link' => '',
                                  'src' => $this->template_image_url . '/Fitness-Logo-1.jpg',
                                  'alt' => 'Fitness-Logo-1',
                                  'fullWidth' => true,
                                  'width' => '180px',
                                  'height' => '96px',
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
              1 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
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
                          'backgroundColor' => '#e6e1e5',
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
                                          'height' => '20px',
                                        ],
                                    ],
                                ],
                              1 =>
                                 [
                                  'type' => 'social',
                                  'iconSet' => 'full-symbol-color',
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
                                          'image' => $this->social_icon_url . '/06-full-symbol-color/Facebook.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Facebook',
                                         ],
                                      1 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'twitter',
                                          'link' => 'http://www.twitter.com',
                                          'image' => $this->social_icon_url . '/06-full-symbol-color/Twitter.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Twitter',
                                         ],
                                      2 =>
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
                      'src' => null,
                      'display' => 'scale',
                    ],
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#e6e1e5',
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
                                  'type' => 'text',
                                  'text' => '<h1 style="text-align: center;"><strong>Get back in the game</strong></h1>',
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
                                  'type' => 'image',
                                  'link' => '',
                                  'src' => $this->template_image_url . '/Fitness-Header.jpg',
                                  'alt' => 'Fitness-Header',
                                  'fullWidth' => true,
                                  'width' => '1280px',
                                  'height' => '696px',
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
              4 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
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
                                          'height' => '40px',
                                        ],
                                    ],
                                ],
                              1 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<h3 style="text-align: center;"><strong>You\'ve left something in your cart...</strong></h3>',
                                ],
                            ],
                        ],
                    ],
                ],
              5 =>
                 [
                  'type' => 'abandonedCartContent',
                  'withLayout' => true,
                  'amount' => '2',
                  'contentType' => 'product',
                  'postStatus' => 'publish',
                  'inclusionType' => 'include',
                  'displayType' => 'full',
                  'titleFormat' => 'h3',
                  'titleAlignment' => 'left',
                  'titleIsLink' => false,
                  'imageFullWidth' => false,
                  'titlePosition' => 'aboveExcerpt',
                  'featuredImagePosition' => 'left',
                  'pricePosition' => 'hidden',
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
                              'padding' => '13px',
                              'borderStyle' => 'solid',
                              'borderWidth' => '3px',
                              'borderColor' => '#aaaaaa',
                            ],
                        ],
                      'context' => 'abandonedCartContent.divider',
                    ],
                  'backgroundColor' => '#ffffff',
                  'backgroundColorAlternate' => '#eeeeee',
                ],
              6 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
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
                                  'type' => 'button',
                                  'text' => 'Recover Cart',
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#ffffff',
                                          'borderColor' => '#343434',
                                          'borderWidth' => '3px',
                                          'borderRadius' => '0px',
                                          'borderStyle' => 'solid',
                                          'width' => '146px',
                                          'lineHeight' => '37px',
                                          'fontColor' => '#343434',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '18px',
                                          'fontWeight' => 'bold',
                                          'textAlign' => 'center',
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
              7 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
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
                          'backgroundColor' => '#afd147',
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
                                          'height' => '30px',
                                        ],
                                    ],
                                ],
                              1 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<h3 style="text-align: center;"><strong>Still interested?</strong></h3>
<p style="text-align: center;"><strong>Here\'s 20% off your order if you complete it right now. We\'re nice like that.</strong></p>',
                                ],
                              2 =>
                                [
                                  'productIds' => [],
                                  'excludedProductIds' => [],
                                  'productCategoryIds' => [],
                                  'excludedProductCategoryIds' => [],
                                  'type' => 'coupon',
                                  'amount' => 10,
                                  'amountMax' => 100,
                                  'discountType' => 'percent',
                                  'expiryDay' => 10,
                                  'usageLimit' => '',
                                  'usageLimitPerUser' => '',
                                  'minimumAmount' => '',
                                  'maximumAmount' => '',
                                  'emailRestrictions' => '',
                                  'styles' => [
                                   'block' => [
                                     'backgroundColor' => '#afd147',
                                     'borderColor' => '#56741d',
                                     'borderWidth' => '3px',
                                     'borderRadius' => '5px',
                                     'borderStyle' => 'solid',
                                     'width' => '219px',
                                     'lineHeight' => '50px',
                                     'fontColor' => '#56741d',
                                     'fontFamily' => 'Courier New',
                                     'fontSize' => '26px',
                                     'fontWeight' => 'bold',
                                     'textAlign' => 'center',
                                   ],
                                  ],
                                  'source' => 'createNew',
                                  'code' => 'XXXX-XXXXXXX-XXXX',
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
              8 =>
                 [
                  'type' => 'container',
                  'columnLayout' => false,
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
                          'backgroundColor' => '#222222',
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
                                          'height' => '40px',
                                        ],
                                    ],
                                ],
                              1 =>
                                 [
                                  'type' => 'image',
                                  'link' => '',
                                  'src' => $this->template_image_url . '/Fitness-Logo-Footer-1.png',
                                  'alt' => 'Fitness-Logo-Footer-1',
                                  'fullWidth' => false,
                                  'width' => '180px',
                                  'height' => '52px',
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
                                  'type' => 'social',
                                  'iconSet' => 'grey',
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
                                          'image' => $this->social_icon_url . '/02-grey/Facebook.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Facebook',
                                         ],
                                      1 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'twitter',
                                          'link' => 'http://www.twitter.com',
                                          'image' => $this->social_icon_url . '/02-grey/Twitter.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Twitter',
                                         ],
                                      2 =>
                                         [
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
                              3 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<p style="text-align: center;"><span style="color: #999999;">Address Line 1, Address Line 2, City, Country</span></p>
<p style="text-align: center;"><span style="color: #999999;"><strong><a href="[link:subscription_unsubscribe_url]" style="color: #999999;">' . __("Unsubscribe", 'mailpoet') . '</a><span>&nbsp;</span>|<span>&nbsp;</span><a href="[link:subscription_manage_url]" style="color: #999999;">' . __("Manage your subscription", 'mailpoet') . '</a></strong></span></p>',
                                 ],
                              4 =>
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
             ],
         ],
      'globalStyles' =>
         [
          'text' =>
             [
              'fontColor' => '#4e4e4e',
              'fontFamily' => 'Arial',
              'fontSize' => '14px',
              'lineHeight' => '1.6',
            ],
          'h1' =>
             [
              'fontColor' => '#4e4e4e',
              'fontFamily' => 'Arial',
              'fontSize' => '40px',
              'lineHeight' => '1.6',
            ],
          'h2' =>
             [
              'fontColor' => '#4e4e4e',
              'fontFamily' => 'Arial',
              'fontSize' => '24px',
              'lineHeight' => '1.6',
            ],
          'h3' =>
             [
              'fontColor' => '#4e4e4e',
              'fontFamily' => 'Arial',
              'fontSize' => '22px',
              'lineHeight' => '1.6',
            ],
          'link' =>
             [
              'fontColor' => '#3c3c3c',
              'textDecoration' => 'underline',
            ],
          'wrapper' =>
             [
              'backgroundColor' => '#ffffff',
            ],
          'body' =>
             [
              'backgroundColor' => '#222222',
            ],
        ],
      'blockDefaults' =>
         [
          'abandonedCartContent' =>
             [
              'amount' => '2',
              'withLayout' => true,
              'contentType' => 'product',
              'postStatus' => 'publish',
              'inclusionType' => 'include',
              'displayType' => 'full',
              'titleFormat' => 'h3',
              'titleAlignment' => 'left',
              'titleIsLink' => false,
              'imageFullWidth' => false,
              'featuredImagePosition' => 'left',
              'pricePosition' => 'hidden',
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
          'automatedLatestContent' =>
             [
              'amount' => '5',
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
              'text' => 'Recover Cart',
              'url' => '',
              'styles' =>
                 [
                  'block' =>
                     [
                      'backgroundColor' => '#ffffff',
                      'borderColor' => '#343434',
                      'borderWidth' => '3px',
                      'borderRadius' => '0px',
                      'borderStyle' => 'solid',
                      'width' => '146px',
                      'lineHeight' => '37px',
                      'fontColor' => '#343434',
                      'fontFamily' => 'Arial',
                      'fontSize' => '18px',
                      'fontWeight' => 'bold',
                      'textAlign' => 'center',
                    ],
                ],
              'type' => 'button',
            ],
          'container' =>
             [
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
              'contentType' => 'post',
              'postStatus' => 'publish',
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
          'products' =>
             [
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
              'readMoreText' => 'Buy now',
              'readMoreButton' =>
                 [
                  'text' => 'Buy now',
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
                      'height' => '40px',
                     ],
                 ],
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
          'woocommerceHeading' =>
             [
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
