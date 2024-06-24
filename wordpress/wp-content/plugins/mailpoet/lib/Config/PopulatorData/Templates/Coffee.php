<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class Coffee {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/coffee';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Coffee", 'mailpoet'),
      'categories' => json_encode(['standard', 'all']),
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
                                          'height' => '50px',
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
                                  'type' => 'image',
                                  'link' => '',
                                  'src' => $this->template_image_url . '/Coffee-Logo-1.png',
                                  'alt' => 'Coffee-Logo-1',
                                  'fullWidth' => false,
                                  'width' => '120px',
                                  'height' => '71px',
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
                                  'type' => 'header',
                                  'text' => '<p><span style="color: #000000;"><a href="[link:newsletter_view_in_browser_url]" style="color: #000000;">' . __("View this in your browser.", 'mailpoet') . '</a></span></p>',
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
                                  'src' => $this->template_image_url . '/Coffee-Header.jpg',
                                  'alt' => 'Coffee-Header',
                                  'fullWidth' => false,
                                  'width' => '1280px',
                                  'height' => '260px',
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
                                  'type' => 'image',
                                  'link' => '',
                                  'src' => $this->template_image_url . '/coffee.gif',
                                  'alt' => 'coffee',
                                  'fullWidth' => true,
                                  'width' => '660px',
                                  'height' => '371px',
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
                                  'type' => 'text',
                                  'text' => '<h2 style="text-align: center;"><strong>We\'ve got some new blends, just for you.</strong></h2>
<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vehicula magna sit amet lorem venenatis, quis condimentum odio egestas. Suspendisse felis felis, tempor id convallis in, tincidunt quis nisi. Morbi dolor elit, maximus et velit quis, fermentum porta lectus. Nulla odio est, tempus vitae nunc sit amet, fermentum tristique lacus. In bibendum fringilla cursus.</p>',
                                 ],
                              1 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Check Them Out',
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#6fb910',
                                          'borderColor' => '#6fb910',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '5px',
                                          'borderStyle' => 'solid',
                                          'width' => '150px',
                                          'lineHeight' => '40px',
                                          'fontColor' => '#ffffff',
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
                                          'borderColor' => '#d6d6d6',
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
                                  'src' => $this->template_image_url . '/Coffee-Mid.jpg',
                                  'alt' => 'Coffee-Mid',
                                  'fullWidth' => true,
                                  'width' => '1280px',
                                  'height' => '411px',
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
                                          'backgroundColor' => '#eaeaea',
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
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#eaeaea',
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
                                  'text' => '<p><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vehicula magna sit amet lorem venenatis, quis condimentum odio egestas. Suspendisse felis felis, tempor id convallis in, tincidunt quis nisi.</span></p>
<p><span></span></p>
<p><span> Morbi dolor elit, maximus et velit quis, fermentum porta lectus. Nulla odio est, tempus vitae nunc sit amet, fermentum tristique lacus. In bibendum fringilla cursus.</span></p>',
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
                                  'type' => 'text',
                                  'text' => '<p><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vehicula magna sit amet lorem venenatis, quis condimentum odio egestas. Suspendisse felis felis, tempor id convallis in, tincidunt quis nisi.</span></p>
<p><span></span></p>
<p><span>Morbi dolor elit, maximus et velit quis, fermentum porta lectus. Nulla odio est, tempus vitae nunc sit amet, fermentum tristique lacus. In bibendum fringilla cursus.</span></p>',
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
                                          'height' => '40px',
                                         ],
                                     ],
                                 ],
                              1 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<h2 style="text-align: center;"><strong>Interested in upgrading your subscription?</strong></h2>
<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
<p style="text-align: center;">Integer vehicula magna sit amet lorem venenatis, quis condimentum odio egestas.</p>',
                                 ],
                              2 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Get Started',
                                  'url' => '',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#6fb910',
                                          'borderColor' => '#6fb910',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '5px',
                                          'borderStyle' => 'solid',
                                          'width' => '118px',
                                          'lineHeight' => '40px',
                                          'fontColor' => '#ffffff',
                                          'fontFamily' => 'Arial',
                                          'fontSize' => '16px',
                                          'fontWeight' => 'bold',
                                          'textAlign' => 'center',
                                         ],
                                     ],
                                 ],
                              3 =>
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
                                          'borderColor' => '#d6d6d6',
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
                                          'height' => '20px',
                                         ],
                                     ],
                                 ],
                              1 =>
                                 [
                                  'type' => 'text',
                                  'text' => '<h2 style="text-align: center;"><strong>Latest News</strong></h2>',
                                 ],
                             ],
                         ],
                     ],
                 ],
              8 =>
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
                                  'link' => 'http://mailpoet.info/cutting-through-the-smog-what-to-do-to-fight-air-pollution/',
                                  'src' => $this->template_image_url . '/Coffee-Cup.jpg',
                                  'alt' => 'Coffee-Cup',
                                  'fullWidth' => false,
                                  'width' => '400px',
                                  'height' => '400px',
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
                                  'text' => '<p style="text-align: left;"><span style="color: #6fb910;"><strong>Capsule MFG Launches Upgraded Line of Modular Coffee Bars</strong></span></p>
<p style="text-align: left;">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
<p style="text-align: left;">Integer vehicula magna sit amet lorem venenatis, quis condimentum odio egestas.</p>
<p style="text-align: left;"></p>
<p><span style="color: #6fb910;"><strong><a href="http://mailpoet.info/" style="color: #6fb910;">Read more</a></strong></span></p>',
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
                                  'type' => 'image',
                                  'link' => 'http://mailpoet.info/cutting-through-the-smog-what-to-do-to-fight-air-pollution/',
                                  'src' => $this->template_image_url . '/Coffee-Cup.jpg',
                                  'alt' => 'Coffee-Cup',
                                  'fullWidth' => false,
                                  'width' => '400px',
                                  'height' => '400px',
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
                                  'text' => '<p style="text-align: left;"><span style="color: #6fb910;"><strong>British-Designed Niche Zero Aims to Leave No Grind Behind</strong></span></p>
<p style="text-align: left;">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
<p style="text-align: left;">Integer vehicula magna sit amet lorem venenatis, quis condimentum odio egestas.</p>
<p style="text-align: left;"></p>
<p><strong><span style="color: #6fb910;"><a href="http://mailpoet.info/" style="color: #6fb910;">Read more</a></span></strong></p>',
                                 ],
                             ],
                         ],
                      2 =>
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
                                  'link' => 'http://mailpoet.info/cutting-through-the-smog-what-to-do-to-fight-air-pollution/',
                                  'src' => $this->template_image_url . '/Coffee-Cup.jpg',
                                  'alt' => 'Coffee-Cup',
                                  'fullWidth' => false,
                                  'width' => '400px',
                                  'height' => '400px',
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
                                  'text' => '<p style="text-align: left;"><span style="color: #6fb910;"><strong>Boyd&rsquo;s Being Acquired by Farmer Bros for Approximately $58.6M</strong></span></p>
<p style="text-align: left;">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
<p style="text-align: left;">Integer vehicula magna sit amet lorem venenatis, quis condimentum odio egestas.</p>
<p style="text-align: left;"></p>
<p><strong><span style="color: #6fb910;"><a href="http://mailpoet.info/" style="color: #6fb910;">Read more</a></span></strong></p>',
                                 ],
                             ],
                         ],
                     ],
                 ],
              9 =>
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
                                  'type' => 'divider',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => 'transparent',
                                          'padding' => '22.5px',
                                          'borderStyle' => 'solid',
                                          'borderWidth' => '1px',
                                          'borderColor' => '#d6d6d6',
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
                                          'height' => '20px',
                                         ],
                                     ],
                                 ],
                              1 =>
                                 [
                                  'type' => 'image',
                                  'link' => '',
                                  'src' => $this->template_image_url . '/Coffee-Logo-1.png',
                                  'alt' => 'Coffee-Logo-1',
                                  'fullWidth' => false,
                                  'width' => '120px',
                                  'height' => '71px',
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
                              3 =>
                                 [
                                  'type' => 'footer',
                                  'text' => '<p><strong><span style="color: #000000;"><a href="[link:subscription_unsubscribe_url]" style="color: #000000;">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]" style="color: #000000;">' . __("Manage your subscription", 'mailpoet') . '</a></span></strong><br />' . __("Add your postal address here!", 'mailpoet') . '</p>',
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
              'fontSize' => '18px',
             ],
          'link' =>
             [
              'fontColor' => '#0f0f0f',
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
