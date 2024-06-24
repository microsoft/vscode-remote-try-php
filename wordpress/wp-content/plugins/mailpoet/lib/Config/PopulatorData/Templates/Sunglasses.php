<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class Sunglasses {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/sunglasses';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Sunglasses", 'mailpoet'),
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
                                  'type' => 'image',
                                  'link' => '',
                                  'src' => $this->template_image_url . '/Glasses-Logo.jpg',
                                  'alt' => 'Glasses-Logo',
                                  'fullWidth' => false,
                                  'width' => '250px',
                                  'height' => '66px',
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
                                  'type' => 'divider',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#ffffff',
                                          'padding' => '17px',
                                          'borderStyle' => 'solid',
                                          'borderWidth' => '2px',
                                          'borderColor' => '#f8b849',
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
                                          'height' => '30px',
                                         ],
                                     ],
                                 ],
                              4 =>
                                 [
                                  'type' => 'image',
                                  'link' => '',
                                  'src' => $this->template_image_url . '/Glasses-Header-2.jpg',
                                  'alt' => 'Glasses-Header-2',
                                  'fullWidth' => true,
                                  'width' => '1280px',
                                  'height' => '116px',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'textAlign' => 'center',
                                         ],
                                     ],
                                 ],
                              5 =>
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
              1 =>
                 [
                  'type' => 'container',
                  'orientation' => 'horizontal',
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
                                  'text' => '<h3 style="text-align: center;"><span style="color: #333333;"><strong>Here\'s what we sent you</strong></span></h3>',
                                 ],
                             ],
                         ],
                     ],
                 ],
              2 =>
                 [
                  'type' => 'container',
                  'orientation' => 'horizontal',
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
                                  'src' => $this->template_image_url . '/Glasses-Images-1.jpg',
                                  'alt' => 'Glasses-Images-1',
                                  'fullWidth' => true,
                                  'width' => '1280px',
                                  'height' => '650px',
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
                                  'text' => '<p style="text-align: center;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur nec nisi quis ex pulvinar molestie. Sed pulvinar placerat justo eu viverra.</span></p>',
                                 ],
                              2 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Choose These Frames',
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#f8b849',
                                          'borderColor' => '#0074a2',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '0px',
                                          'borderStyle' => 'solid',
                                          'width' => '195px',
                                          'lineHeight' => '40px',
                                          'fontColor' => '#ffffff',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '16px',
                                          'fontWeight' => 'normal',
                                          'textAlign' => 'center',
                                         ],
                                     ],
                                 ],
                              3 =>
                                 [
                                  'type' => 'image',
                                  'link' => '',
                                  'src' => $this->template_image_url . '/Glasses-Images-2.jpg',
                                  'alt' => 'Glasses-Images-2',
                                  'fullWidth' => true,
                                  'width' => '1280px',
                                  'height' => '650px',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'textAlign' => 'center',
                                         ],
                                     ],
                                 ],
                              4 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<p style="text-align: center;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur nec nisi quis ex pulvinar molestie. Sed pulvinar placerat justo eu viverra.</span></p>',
                                 ],
                              5 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Choose These Frames',
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#f8b849',
                                          'borderColor' => '#0074a2',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '0px',
                                          'borderStyle' => 'solid',
                                          'width' => '195px',
                                          'lineHeight' => '40px',
                                          'fontColor' => '#ffffff',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '16px',
                                          'fontWeight' => 'normal',
                                          'textAlign' => 'center',
                                         ],
                                     ],
                                 ],
                              6 =>
                                 [
                                  'type' => 'image',
                                  'link' => '',
                                  'src' => $this->template_image_url . '/Glasses-Images-3.jpg',
                                  'alt' => 'Glasses-Images-3',
                                  'fullWidth' => true,
                                  'width' => '1280px',
                                  'height' => '650px',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'textAlign' => 'center',
                                         ],
                                     ],
                                 ],
                              7 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<p style="text-align: center;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur nec nisi quis ex pulvinar molestie. Sed pulvinar placerat justo eu viverra.</span></p>',
                                 ],
                              8 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Choose These Frames',
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#f8b849',
                                          'borderColor' => '#0074a2',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '0px',
                                          'borderStyle' => 'solid',
                                          'width' => '195px',
                                          'lineHeight' => '40px',
                                          'fontColor' => '#ffffff',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '16px',
                                          'fontWeight' => 'normal',
                                          'textAlign' => 'center',
                                         ],
                                     ],
                                 ],
                              9 =>
                                 [
                                  'type' => 'divider',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#ffffff',
                                          'padding' => '34.5px',
                                          'borderStyle' => 'solid',
                                          'borderWidth' => '2px',
                                          'borderColor' => '#f8b849',
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
                                  'src' => $this->template_image_url . '/Glasses-Header.jpg',
                                  'alt' => 'Glasses-Header',
                                  'fullWidth' => true,
                                  'width' => '640px',
                                  'height' => '920px',
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
                                          'height' => '60px',
                                         ],
                                     ],
                                 ],
                              1 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<h3><strong>Our Summer Range Is Here</strong></h3>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur nec nisi quis ex pulvinar molestie. Sed pulvinar placerat justo eu viverra. Pellentesque in interdum eros, a venenatis velit.</p>
<p></p>
<p>Fusce finibus convallis augue, ut viverra felis placerat in. Curabitur et commodo ipsum. Mauris tellus metus, tristique vel sollicitudin ut, malesuada in augue.&nbsp;</p>',
                                 ],
                              2 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Find Out More',
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#f8b849',
                                          'borderColor' => '#0074a2',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '0px',
                                          'borderStyle' => 'solid',
                                          'width' => '137px',
                                          'lineHeight' => '40px',
                                          'fontColor' => '#ffffff',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '16px',
                                          'fontWeight' => 'normal',
                                          'textAlign' => 'left',
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
                  'orientation' => 'horizontal',
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
                                          'backgroundColor' => '#ffffff',
                                          'padding' => '34.5px',
                                          'borderStyle' => 'solid',
                                          'borderWidth' => '2px',
                                          'borderColor' => '#f8b849',
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
                                  'text' => '<h2 style="text-align: center;"><span style="color: #333333;"><strong>Got any questions or need some help?</strong></span></h2>
<p style="text-align: center;"><span style="color: #333333;">We\'re just a click or a phone call away.</span></p>
<p style="text-align: center;"><span style="color: #333333;"></span></p>
<h3 style="text-align: center;"><span style="color: #333333;"><strong>Call Us:</strong> 08856877854</span></h3>
<h3 style="text-align: center;"></h3>',
                                 ],
                              1 =>
                                 [
                                  'type' => 'divider',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#ffffff',
                                          'padding' => '23.5px',
                                          'borderStyle' => 'solid',
                                          'borderWidth' => '2px',
                                          'borderColor' => '#f8b849',
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
                                      3 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'email',
                                          'link' => '',
                                          'image' => $this->social_icon_url . '/07-full-symbol-black/Email.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Email',
                                         ],
                                     ],
                                 ],
                              1 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<p style="text-align: center; font-size: 11px;"><strong><span style="color: #808080;"><a href="[link:subscription_unsubscribe_url]" style="color: #808080;">' . __("Unsubscribe", 'mailpoet') . '</a>&nbsp;|&nbsp;<a href="[link:subscription_manage_url]" style="color: #808080;">' . __("Manage your subscription", 'mailpoet') . '</a></span></strong><br /><span style="color: #808080;">' . __("Add your postal address here!", 'mailpoet') . '</span></p>',
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
              'backgroundColor' => '#ffffff',
             ],
         ],
    ];
  }
}
