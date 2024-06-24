<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class LifestyleBlogB {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/lifestyle-blog-b';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Lifestyle Blog", 'mailpoet'),
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
                        'height' => '30px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/WomensLifestyle-Logo-1.png',
                    'alt' => 'WomensLifestyle-Logo',
                    'fullWidth' => true,
                    'width' => '160px',
                    'height' => '57px',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'textAlign' => 'left',
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
                  1 =>
                   [
                    'type' => 'header',
                    'text' => '<p><span style="color: #b25088;"><a href="[link:newsletter_view_in_browser_url]" style="color: #b25088;">' . __("View this in your browser.", 'mailpoet') . '</a></span></p>',
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
                        'textAlign' => 'right',
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
                        'height' => '30px',
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
              'src' => $this->template_image_url . '/WomensLifestyle-Header.jpg',
              'display' => 'scale',
             ],
            'styles' =>
             [
              'block' =>
               [
                'backgroundColor' => '#b25187',
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
                        'height' => '440px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/WomensLifestyle-HeaderDrop.jpg',
                    'alt' => 'WomensLifestyle-HeaderDrop',
                    'fullWidth' => true,
                    'width' => '900px',
                    'height' => '61px',
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
                    'type' => 'text',
                    'text' => '<p style="font-size: 11px; letter-spacing: 4px; text-align: center;"><span style="color: #808080;"><strong>FEATURED ARTICLE THIS MONTH</strong></span></p>
    <h2 style="text-align: center;"><strong>Business tips&nbsp;from top female executives</strong></h2>',
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
                    'type' => 'text',
                    'text' => '<h3 style="text-align: center;"><span style="color: #993366;"><strong>1</strong></span></h3>
    <p style="text-align: center;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam non est sed tellus pharetra facilisis quis vel lacus.</span></p>',
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
                    'type' => 'text',
                    'text' => '<h3 style="text-align: center;"><span style="color: #993366;"><strong>2</strong></span></h3>
    <p style="text-align: center;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam non est sed tellus pharetra facilisis quis vel lacus.</span></p>',
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
                    'text' => '<h3 style="text-align: center;"><span style="color: #993366;"><strong>3</strong></span></h3>
    <p style="text-align: center;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam non est sed tellus pharetra facilisis quis vel lacus.</span></p>',
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
                    'type' => 'spacer',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => 'transparent',
                        'height' => '23px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'button',
                    'text' => 'Read the article',
                    'url' => '',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#b25088',
                        'borderColor' => '#0074a2',
                        'borderWidth' => '0px',
                        'borderRadius' => '0px',
                        'borderStyle' => 'solid',
                        'width' => '180px',
                        'lineHeight' => '50px',
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
                    'type' => 'spacer',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => 'transparent',
                        'height' => '21px',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/WomensLifestyle-Image-1.jpg',
                    'alt' => 'WomensLifestyle-Image-1',
                    'fullWidth' => true,
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
                        'height' => '71px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<p style="font-size: 11px; letter-spacing: 4px; text-align: left;"><span style="color: #808080;"><strong>PODCASTS</strong></span></p>
    <h2 style="text-align: left;"><strong>Does hard work really pay off?</strong></h2>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam non est sed tellus pharetra facilisis quis vel lacus.</p>',
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
                        'height' => '30px',
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
                        'height' => '71px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<p style="font-size: 11px; letter-spacing: 4px; text-align: left;"><span style="color: #808080;"><strong>PSYCHOLOGY</strong></span></p>
    <h2 style="text-align: left;"><strong>Closing deals can be easier than you think</strong></h2>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam non est sed tellus pharetra facilisis quis vel lacus.</p>',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/WomensLifestyle-Image-2.jpg',
                    'alt' => 'WomensLifestyle-Image-2',
                    'fullWidth' => true,
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
                        'height' => '30px',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/WomensLifestyle-Image-3.jpg',
                    'alt' => 'WomensLifestyle-Image-3',
                    'fullWidth' => true,
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
                        'height' => '71px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<p style="font-size: 11px; letter-spacing: 4px; text-align: left;"><span style="color: #808080;"><strong>TECHNOLOGY</strong></span></p>
    <h2 style="text-align: left;"><strong>What email software is right for you?</strong></h2>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam non est sed tellus pharetra facilisis quis vel lacus.</p>',
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
                        'height' => '50px',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/WomensLifestyle-Logo-1.png',
                    'alt' => 'WomensLifestyle-Logo',
                    'fullWidth' => false,
                    'width' => '112px',
                    'height' => '57px',
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
                    'type' => 'social',
                    'iconSet' => 'circles',
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
                      2 =>
                       [
                        'type' => 'socialIcon',
                        'iconType' => 'website',
                        'link' => '',
                        'image' => $this->social_icon_url . '/03-circles/Website.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Website',
                       ],
                      3 =>
                       [
                        'type' => 'socialIcon',
                        'iconType' => 'pinterest',
                        'link' => 'http://www.pinterest.com',
                        'image' => $this->social_icon_url . '/03-circles/Pinterest.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Pinterest',
                       ],
                     ],
                   ],
                  2 =>
                   [
                    'type' => 'footer',
                    'text' => '<p><strong><span style="color: #b25088;"><a href="[link:subscription_unsubscribe_url]" style="color: #b25088;">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]" style="color: #b25088;">' . __("Manage your subscription", 'mailpoet') . '</a></span></strong><br />' . __("Add your postal address here!", 'mailpoet') . '</p>',
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
          'text' => 'Read the article',
          'url' => '',
          'styles' =>
           [
            'block' =>
             [
              'backgroundColor' => '#b25088',
              'borderColor' => '#0074a2',
              'borderWidth' => '0px',
              'borderRadius' => '0px',
              'borderStyle' => 'solid',
              'width' => '180px',
              'lineHeight' => '50px',
              'fontColor' => '#ffffff',
              'fontFamily' => 'Arial',
              'fontSize' => '16px',
              'fontWeight' => 'bold',
              'textAlign' => 'center',
             ],
           ],
          'type' => 'button',
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
          'type' => 'footer',
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
          'iconSet' => 'circles',
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
            2 =>
             [
              'type' => 'socialIcon',
              'iconType' => 'website',
              'link' => '',
              'image' => $this->social_icon_url . '/03-circles/Website.png',
              'height' => '32px',
              'width' => '32px',
              'text' => 'Website',
             ],
            3 =>
             [
              'type' => 'socialIcon',
              'iconType' => 'pinterest',
              'link' => 'http://www.pinterest.com',
              'image' => $this->social_icon_url . '/03-circles/Pinterest.png',
              'height' => '32px',
              'width' => '32px',
              'text' => 'Pinterest',
             ],
           ],
          'type' => 'social',
         ],
        'spacer' =>
         [
          'styles' =>
           [
            'block' =>
             [
              'backgroundColor' => 'transparent',
              'height' => '30px',
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
              'textAlign' => 'right',
             ],
            'link' =>
             [
              'fontColor' => '#6cb7d4',
              'textDecoration' => 'underline',
             ],
           ],
          'type' => 'header',
         ],
       ],
    ];
  }
}
