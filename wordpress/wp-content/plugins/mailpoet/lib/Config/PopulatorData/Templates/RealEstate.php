<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class RealEstate {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/real-estate';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Real Estate", 'mailpoet'),
      'categories' => json_encode(['welcome', 'all']),
      'readonly' => 1,
      'thumbnail' => $this->getThumbnail(),
      'body' => json_encode($this->getBody()),
    ];
  }

  private function getThumbnail() {
    return $this->template_image_url . '/thumbnail.20190411-1500.jpg';
  }

  private function getBody() {
    return [
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
                          'backgroundColor' => '#8691be',
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
                              [
                                "type" => "spacer",
                                "styles" => [
                                  "block" => [
                                    "backgroundColor" => "transparent",
                                    "height" => "20px",
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
                                  'src' => $this->template_image_url . '/Property-Logo-1-150x84.jpg',
                                  'alt' => 'Property-Logo-1',
                                  'fullWidth' => false,
                                  'width' => '150px',
                                  'height' => '84px',
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
                                  'type' => 'spacer',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => 'transparent',
                                          'height' => '25px',
                                         ],
                                     ],
                                 ],
                              1 =>
                                 [
                                  'type' => 'social',
                                  'iconSet' => 'full-symbol-grey',
                                  'icons' =>
                                     [
                                      0 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'facebook',
                                          'link' => 'http://www.facebook.com',
                                          'image' => $this->social_icon_url . '/08-full-symbol-grey/Facebook.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Facebook',
                                         ],
                                      1 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'twitter',
                                          'link' => 'http://www.twitter.com',
                                          'image' => $this->social_icon_url . '/08-full-symbol-grey/Twitter.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Twitter',
                                         ],
                                      2 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'website',
                                          'link' => '',
                                          'image' => $this->social_icon_url . '/08-full-symbol-grey/Website.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Website',
                                         ],
                                      3 =>
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
              1 =>
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
                          'backgroundColor' => '#8691be',
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
                                          'height' => '25px',
                                         ],
                                     ],
                                 ],
                              1 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<p style="text-align: center;"><strong><span style="color: #ffffff;">TAKING&nbsp;THE WORRY OUT OF REAL ESTATE</span></strong></p>',
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
                              3 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<h1 style="text-align: center;"><span style="color: #ffffff;">Welcome Home</span></h1>',
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
                                  'type' => 'image',
                                  'link' => '',
                                  'src' => $this->template_image_url . '/Property-Header.jpg',
                                  'alt' => 'Property-Header',
                                  'fullWidth' => true,
                                  'width' => '1280px',
                                  'height' => '782px',
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
                              2 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<h3 style="text-align: center;">We\'re here to help you move</h3>',
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
                                  'src' => $this->template_image_url . '/Property-Icon-1.png',
                                  'alt' => 'Property-Icon-1',
                                  'fullWidth' => false,
                                  'width' => '48px',
                                  'height' => '48px',
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
                                  'text' => '<p style="text-align: center; font-size: 12px;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur nec nisi quis ex.</span></p>',
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
                                  'src' => $this->template_image_url . '/Property-Icon-2.png',
                                  'alt' => 'Property-Icon-2',
                                  'fullWidth' => false,
                                  'width' => '48px',
                                  'height' => '48px',
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
                                  'text' => '<p style="text-align: center; font-size: 12px;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur nec nisi quis ex.</span></p>',
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
                                  'src' => $this->template_image_url . '/Property-Icon-3.png',
                                  'alt' => 'Property-Icon-3',
                                  'fullWidth' => false,
                                  'width' => '48px',
                                  'height' => '48px',
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
                                  'text' => '<p style="text-align: center; font-size: 12px;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur nec nisi quis ex.</span></p>',
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
                                  'type' => 'divider',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => 'transparent',
                                          'padding' => '22.5px',
                                          'borderStyle' => 'solid',
                                          'borderWidth' => '1px',
                                          'borderColor' => '#8691be',
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
                                  'type' => 'text',
                                  'text' => '<p style="text-align: center;"><span style="color: #333333;">We\'d love to keep you updated with our latest news. </span></p>
<p style="text-align: center;"><span style="color: #333333;">Sign up to our newsletter now and you\'ll never miss a thing.</span></p>',
                                 ],
                              1 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Sign Up Now',
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#8691be',
                                          'borderColor' => '#0074a2',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '40px',
                                          'borderStyle' => 'solid',
                                          'width' => '180px',
                                          'lineHeight' => '40px',
                                          'fontColor' => '#ffffff',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '18px',
                                          'fontWeight' => 'bold',
                                          'textAlign' => 'center',
                                         ],
                                     ],
                                 ],
                              2 =>
                                 [
                                  'type' => 'spacer',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => 'transparent',
                                          'height' => '24px',
                                         ],
                                     ],
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
                          'backgroundColor' => '#6a7087',
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
                                          'height' => '25px',
                                         ],
                                     ],
                                 ],
                              1 =>
                                 [
                                  'type' => 'footer',
                                  'text' => '<p><strong><span style="color: #ffffff;"><a href="[link:subscription_unsubscribe_url]" style="color: #ffffff;">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]" style="color: #ffffff;">' . __("Manage your subscription", 'mailpoet') . '</a></span></strong><br /><span style="color: #ffffff;">' . __("Add your postal address here!", 'mailpoet') . '</span></p>',
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
                              2 =>
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
              'fontSize' => '15px',
             ],
          'h1' =>
             [
              'fontColor' => '#111111',
              'fontFamily' => 'Arial',
              'fontSize' => '40px',
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
              'fontFamily' => 'Arial',
              'fontSize' => '22px',
             ],
          'link' =>
             [
              'fontColor' => '#21759B',
              'textDecoration' => 'underline',
             ],
          'wrapper' =>
             [
              'backgroundColor' => '#ffffff',
             ],
          'body' =>
             [
              'backgroundColor' => '#6a7087',
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
                      'height' => '20px',
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
