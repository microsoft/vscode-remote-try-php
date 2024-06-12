<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class CityLocalNews {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/city-local-news';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("City News", 'mailpoet'),
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
                'backgroundColor' => '#252525',
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
                    'type' => 'header',
                    'text' => '<p><span style="color: #999999;"><a href="[link:newsletter_view_in_browser_url]" style="color: #999999;">' . __("View this in your browser.", 'mailpoet') . '</a></span></p>',
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
              'src' => $this->template_image_url . '/CityNews-Header.jpg',
              'display' => 'scale',
             ],
            'styles' =>
             [
              'block' =>
               [
                'backgroundColor' => '#000000',
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
                        'height' => '70px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/CityNews-Logo.png',
                    'alt' => 'CityNews-Logo',
                    'fullWidth' => false,
                    'width' => '150px',
                    'height' => '178px',
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
                    'type' => 'text',
                    'text' => '<h1 style="text-align: center;"><span style="color: #ffffff;"><strong>[date:mtext] [date:y]</strong></span></h1>
    <p style="text-align: center;"><span style="color: #ffffff;">N E W S L E T T E R</span></p>',
                   ],
                  3 =>
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
                        'iconType' => 'email',
                        'link' => '',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Email.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Email',
                       ],
                      3 =>
                       [
                        'type' => 'socialIcon',
                        'iconType' => 'youtube',
                        'link' => 'http://www.youtube.com',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Youtube.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Youtube',
                       ],
                     ],
                   ],
                  4 =>
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
                'backgroundColor' => '#252525',
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
                    'type' => 'spacer',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => 'transparent',
                        'height' => '22px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<p style="text-align: left;"><span style="color: #333333;"><strong><span></span></strong></span><span style="color: #999999;"><strong>TOP STORY</strong></span></p>
    <h2 style="text-align: left;"><span style="color: #333333;"><strong>Park opens to all within the community</strong></span></h2>
    <p><span style="color: #333333;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ac maximus justo. Phasellus ullamcorper, enim at eleifend malesuada, risus purus interdum arcu, sed porttitor erat diam a neque.&nbsp;</span></p>
    <p><span style="color: #333333;"></span></p>
    <p><strong><a href="http://mailpoet-sg.com">Read full article &gt;</a></strong></p>',
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
                    'src' => $this->template_image_url . '/CityNews-Images-1.jpg',
                    'alt' => 'CityNews-Images-1',
                    'fullWidth' => true,
                    'width' => '600px',
                    'height' => '600px',
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
                'backgroundColor' => '#252525',
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
                        'height' => '20px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/CityNews-ThinImages-1.jpg',
                    'alt' => 'CityNews-ThinImages-1',
                    'fullWidth' => false,
                    'width' => '600px',
                    'height' => '300px',
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
                    'type' => 'text',
                    'text' => '<p style="text-align: left;"><span style="color: #999999;"><strong>EVENTS</strong></span></p>
    <h3 style="text-align: left;"><span style="color: #333333;"><strong>Christmas Market opening soon</strong></span></h3>
    <p><strong><a href="http://mailpoet-sg.com">Read full article &gt;</a></strong></p>',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/CityNews-ThinImages-2.jpg',
                    'alt' => 'CityNews-ThinImages-2',
                    'fullWidth' => false,
                    'width' => '600px',
                    'height' => '300px',
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
                    'type' => 'text',
                    'text' => '<p style="text-align: left;"><span style="color: #999999;"><strong>HEALTHCARE</strong></span></p>
    <h3 style="text-align: left;"><span style="color: #333333;"><strong>Hospitals brace for cuts to services</strong></span></h3>
    <p><strong><a href="http://mailpoet-sg.com">Read full article &gt;</a></strong></p>',
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
                    'src' => $this->template_image_url . '/CityNews-ThinImages-3.jpg',
                    'alt' => 'CityNews-ThinImages-3',
                    'fullWidth' => false,
                    'width' => '600px',
                    'height' => '300px',
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
                    'type' => 'text',
                    'text' => '<p style="text-align: left;"><span style="color: #999999;"><strong>WEATHER</strong></span></p>
    <h3 style="text-align: left;"><span style="color: #333333;"><strong>Dry heat has a high chance to cause fires</strong></span></h3>
    <p><strong><a href="http://mailpoet-sg.com">Read full article &gt;</a></strong></p>',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/CityNews-ThinImages-4.jpg',
                    'alt' => 'CityNews-ThinImages-4',
                    'fullWidth' => false,
                    'width' => '600px',
                    'height' => '300px',
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
                    'type' => 'text',
                    'text' => '<p style="text-align: left;"><span style="color: #999999;"><strong>ARTS</strong></span></p>
    <h3 style="text-align: left;"><span style="color: #333333;"><strong>Local theatre to stage dramatic new play</strong></span></h3>
    <p><strong><a href="http://mailpoet-sg.com">Read full article &gt;</a></strong></p>',
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
                'backgroundColor' => '#252525',
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
                'backgroundColor' => '#252525',
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
                    'src' => $this->template_image_url . '/CityNews-Phone.png',
                    'alt' => 'CityNews-Phone',
                    'fullWidth' => false,
                    'width' => '162px',
                    'height' => '679px',
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
                        'height' => '20px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<h3 style="text-align: left;"><span style="color: #ffffff;"><strong>Download the CityNews app</strong></span></h3>
    <p><span style="color: #d1d1d1;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ac maximus justo. Phasellus ullamcorper, enim at eleifend malesuada, risus purus interdum arcu, sed porttitor erat diam a neque.&nbsp;</span></p>',
                   ],
                  2 =>
                   [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/CityNews-GoogleDownload.png',
                    'alt' => 'CityNews-GoogleDownload',
                    'fullWidth' => false,
                    'width' => '150px',
                    'height' => '168px',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'textAlign' => 'left',
                       ],
                     ],
                   ],
                  3 =>
                   [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/CityNews-AppleDownload.png',
                    'alt' => 'CityNews-AppleDownload',
                    'fullWidth' => false,
                    'width' => '150px',
                    'height' => '168px',
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
                'backgroundColor' => '#252525',
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
                    'type' => 'divider',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => 'transparent',
                        'padding' => '13px',
                        'borderStyle' => 'solid',
                        'borderWidth' => '1px',
                        'borderColor' => '#4b4b4b',
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
                  2 =>
                   [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/CityNews-Logo.png',
                    'alt' => 'CityNews-Logo',
                    'fullWidth' => false,
                    'width' => '122px',
                    'height' => '178px',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'textAlign' => 'center',
                       ],
                     ],
                   ],
                  3 =>
                   [
                    'type' => 'footer',
                    'text' => '<p><span style="color: #999999;"><a href="[link:subscription_unsubscribe_url]" style="color: #999999;">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]" style="color: #999999;">' . __("Manage your subscription", 'mailpoet') . '</a></span><br /><span style="color: #ffffff;">' . __("Add your postal address here!", 'mailpoet') . '</span></p>',
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
                  4 =>
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
                        'iconType' => 'email',
                        'link' => '',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Email.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Email',
                       ],
                      3 =>
                       [
                        'type' => 'socialIcon',
                        'iconType' => 'youtube',
                        'link' => 'http://www.youtube.com',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Youtube.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Youtube',
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
          'fontFamily' => 'Source Sans Pro',
          'fontSize' => '16px',
         ],
        'h1' =>
         [
          'fontColor' => '#111111',
          'fontFamily' => 'Merriweather Sans',
          'fontSize' => '26px',
         ],
        'h2' =>
         [
          'fontColor' => '#222222',
          'fontFamily' => 'Merriweather',
          'fontSize' => '24px',
         ],
        'h3' =>
         [
          'fontColor' => '#333333',
          'fontFamily' => 'Merriweather',
          'fontSize' => '20px',
         ],
        'link' =>
         [
          'fontColor' => '#252525',
          'textDecoration' => 'underline',
         ],
        'wrapper' =>
         [
          'backgroundColor' => '#f8f8f8',
         ],
        'body' =>
         [
          'backgroundColor' => '#252525',
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
          'text' => 'Read more',
          'url' => '[postLink]',
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
              'borderWidth' => '1px',
              'borderColor' => '#4b4b4b',
             ],
           ],
          'type' => 'divider',
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
          'featuredImagePosition' => 'left',
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
            'type' => 'button',
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
                'borderWidth' => '1px',
                'borderColor' => '#4b4b4b',
               ],
             ],
            'type' => 'divider',
           ],
          'backgroundColor' => '#ffffff',
          'backgroundColorAlternate' => '#eeeeee',
          'type' => 'posts',
          'offset' => 0,
          'terms' =>
           [
           ],
          'search' => '',
         ],
        'social' =>
         [
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
              'iconType' => 'email',
              'link' => '',
              'image' => $this->social_icon_url . '/08-full-symbol-grey/Email.png',
              'height' => '32px',
              'width' => '32px',
              'text' => 'Email',
             ],
            3 =>
             [
              'type' => 'socialIcon',
              'iconType' => 'youtube',
              'link' => 'http://www.youtube.com',
              'image' => $this->social_icon_url . '/08-full-symbol-grey/Youtube.png',
              'height' => '32px',
              'width' => '32px',
              'text' => 'Youtube',
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
              'height' => '40px',
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
