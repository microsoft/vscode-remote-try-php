<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class PrimarySchool {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/primary-school';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Primary School", 'mailpoet'),
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
                'backgroundColor' => '#cae7f2',
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
                    'type' => 'header',
                    'text' => '<p><strong><a href="[link:newsletter_view_in_browser_url]">' . __("View this in your browser.", 'mailpoet') . '</a></strong></p>',
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
                        'fontColor' => '#e63729',
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
              'src' => $this->template_image_url . '/PrimarySchool-Header.jpg',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/PrimarySchool-Logo.png',
                    'alt' => 'PrimarySchool-Logo',
                    'fullWidth' => false,
                    'width' => '120px',
                    'height' => '652px',
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
                        'height' => '33px',
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
                'backgroundColor' => '#e63729',
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
                    'text' => '<p style="text-align: center;"><strong><span style="color: #ffffff;"><a href="http://mailpoet-sg.com" style="color: #ffffff;">News &amp; Events</a></span></strong></p>',
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
                    'text' => '<p style="text-align: center;"><strong><span style="color: #ffffff;"><a href="http://mailpoet-sg.com" style="color: #ffffff;">Parent Information</a></span></strong></p>',
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
                    'text' => '<p style="text-align: center;"><strong><span style="color: #ffffff;"><a href="http://mailpoet-sg.com" style="color: #ffffff;">Scho</a><a href="http://mailpoet-sg.com" style="color: #ffffff;">ol Calendar</a></span></strong></p>',
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
              'src' => $this->template_image_url . '/PrimarySchool-WideImage-1.jpg',
              'display' => 'scale',
             ],
            'styles' =>
             [
              'block' =>
               [
                'backgroundColor' => '#c23a3a',
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
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<h1 style="text-align: center;"><strong><span style="color: #ffffff;">Welcome back everyone!</span></strong></h1>
    <p style="text-align: center;"><span style="color: #ffffff;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ac orci neque. Sed hendrerit condimentum augue, sed porta nulla vulputate rutrum. Donec vel purus urna.</span></p>',
                   ],
                  2 =>
                   [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/PrimarySchool-Avatar.png',
                    'alt' => 'PrimarySchool-Avatar',
                    'fullWidth' => false,
                    'width' => '80px',
                    'height' => '500px',
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
                    'type' => 'text',
                    'text' => '<p style="text-align: center;"><em><span style="color: #ffffff;">Dr Angela Boyd - Director of Schooling</span></em></p>',
                   ],
                  4 =>
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
            'columnLayout' => '2_1',
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
                    'text' => '<h1 style="text-align: left;"><span style="color: #333333;"><strong>The new school year is here</strong></span></h1>
    <p style="text-align: left;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ac orci neque. Sed hendrerit condimentum augue, sed porta nulla vulputate rutrum. Donec vel purus urna. </span></p>
    <p style="text-align: left;"><span></span></p>
    <p style="text-align: left;"><span>Sed dui sapien, congue non accumsan euismod, ultrices ut dui. Ut sapien arcu, interdum pharetra enim at, porttitor viverra nisi. Suspendisse potenti.&nbsp;</span></p>',
                   ],
                  1 =>
                   [
                    'type' => 'divider',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => 'transparent',
                        'padding' => '13px',
                        'borderStyle' => 'dotted',
                        'borderWidth' => '4px',
                        'borderColor' => '#e63729',
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
                        'height' => '20px',
                       ],
                     ],
                   ],
                  3 =>
                   [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/PrimarySchool-CopyImage.jpg',
                    'alt' => 'PrimarySchool-CopyImage',
                    'fullWidth' => false,
                    'width' => '1000px',
                    'height' => '583px',
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
                    'text' => '<h2 style="text-align: left;"><span style="color: #333333;"><strong>Essentials for your bag</strong></span></h2>
    <p style="text-align: left;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ac orci neque. Sed hendrerit condimentum augue, sed porta nulla vulputate rutrum. Donec vel purus urna. </span></p>',
                   ],
                  5 =>
                   [
                    'type' => 'button',
                    'text' => 'Get the kit',
                    'url' => '',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#e63729',
                        'borderColor' => '#0074a2',
                        'borderWidth' => '0px',
                        'borderRadius' => '40px',
                        'borderStyle' => 'solid',
                        'width' => '90px',
                        'lineHeight' => '33px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '14px',
                        'fontWeight' => 'normal',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/PrimarySchool-School.png',
                    'alt' => 'PrimarySchool-School',
                    'fullWidth' => false,
                    'width' => '150px',
                    'height' => '512px',
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
                    'type' => 'button',
                    'text' => 'Class Lists',
                    'url' => '',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#e63729',
                        'borderColor' => '#0074a2',
                        'borderWidth' => '0px',
                        'borderRadius' => '40px',
                        'borderStyle' => 'solid',
                        'width' => '160px',
                        'lineHeight' => '38px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '16px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'center',
                       ],
                     ],
                   ],
                  2 =>
                   [
                    'type' => 'button',
                    'text' => 'Payments',
                    'url' => '',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#43772f',
                        'borderColor' => '#0074a2',
                        'borderWidth' => '0px',
                        'borderRadius' => '40px',
                        'borderStyle' => 'solid',
                        'width' => '160px',
                        'lineHeight' => '38px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '16px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'center',
                       ],
                     ],
                   ],
                  3 =>
                   [
                    'type' => 'button',
                    'text' => 'Schedule',
                    'url' => '',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#408bb6',
                        'borderColor' => '#0074a2',
                        'borderWidth' => '0px',
                        'borderRadius' => '40px',
                        'borderStyle' => 'solid',
                        'width' => '160px',
                        'lineHeight' => '38px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '16px',
                        'fontWeight' => 'normal',
                        'textAlign' => 'center',
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
                'backgroundColor' => '#e63729',
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
                    'type' => 'text',
                    'text' => '<h1 style="text-align: center;"><strong><span style="color: #ffffff;">Everything is social!</span></strong></h1>
    <p style="text-align: center;"><span style="color: #ffffff;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ac orci neque. Sed hendrerit condimentum augue, sed porta nulla vulputate rutrum. Donec vel purus urna.</span></p>',
                   ],
                  2 =>
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
                        'iconType' => 'youtube',
                        'link' => 'http://www.youtube.com',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Youtube.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Youtube',
                       ],
                      3 =>
                       [
                        'type' => 'socialIcon',
                        'iconType' => 'website',
                        'link' => '',
                        'image' => $this->social_icon_url . '/08-full-symbol-grey/Website.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Website',
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
                'backgroundColor' => '#cae7f2',
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
                    'src' => $this->template_image_url . '/PrimarySchool-Logo.png',
                    'alt' => 'PrimarySchool-Logo',
                    'fullWidth' => false,
                    'width' => '110px',
                    'height' => '652px',
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
                        'height' => '30px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'footer',
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
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '12px',
                        'textAlign' => 'right',
                       ],
                      'link' =>
                       [
                        'fontColor' => '#e63729',
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
      'globalStyles' =>
       [
        'text' =>
         [
          'fontColor' => '#000000',
          'fontFamily' => 'Merriweather Sans',
          'fontSize' => '14px',
         ],
        'h1' =>
         [
          'fontColor' => '#111111',
          'fontFamily' => 'Merriweather Sans',
          'fontSize' => '30px',
         ],
        'h2' =>
         [
          'fontColor' => '#222222',
          'fontFamily' => 'Merriweather Sans',
          'fontSize' => '24px',
         ],
        'h3' =>
         [
          'fontColor' => '#333333',
          'fontFamily' => 'Merriweather Sans',
          'fontSize' => '22px',
         ],
        'link' =>
         [
          'fontColor' => '#ffffff',
          'textDecoration' => 'underline',
         ],
        'wrapper' =>
         [
          'backgroundColor' => '#ffffff',
         ],
        'body' =>
         [
          'backgroundColor' => '#cae7f2',
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
          'text' => 'Get the kit',
          'url' => '',
          'styles' =>
           [
            'block' =>
             [
              'backgroundColor' => '#e63729',
              'borderColor' => '#0074a2',
              'borderWidth' => '0px',
              'borderRadius' => '40px',
              'borderStyle' => 'solid',
              'width' => '90px',
              'lineHeight' => '33px',
              'fontColor' => '#ffffff',
              'fontFamily' => 'Merriweather Sans',
              'fontSize' => '14px',
              'fontWeight' => 'normal',
              'textAlign' => 'left',
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
              'borderStyle' => 'dotted',
              'borderWidth' => '4px',
              'borderColor' => '#e63729',
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
              'fontFamily' => 'Merriweather Sans',
              'fontSize' => '12px',
              'textAlign' => 'right',
             ],
            'link' =>
             [
              'fontColor' => '#e63729',
              'textDecoration' => 'underline',
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
              'iconType' => 'youtube',
              'link' => 'http://www.youtube.com',
              'image' => $this->social_icon_url . '/08-full-symbol-grey/Youtube.png',
              'height' => '32px',
              'width' => '32px',
              'text' => 'Youtube',
             ],
            3 =>
             [
              'type' => 'socialIcon',
              'iconType' => 'website',
              'link' => '',
              'image' => $this->social_icon_url . '/08-full-symbol-grey/Website.png',
              'height' => '32px',
              'width' => '32px',
              'text' => 'Website',
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
              'textAlign' => 'center',
             ],
            'link' =>
             [
              'fontColor' => '#e63729',
              'textDecoration' => 'underline',
             ],
           ],
          'type' => 'header',
         ],
       ],
    ];
  }
}
