<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class Motor {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/motor';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Motor", 'mailpoet'),
      'categories' => json_encode(['woocommerce', 'all']),
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
                'backgroundColor' => '#e3e3e3',
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
                'backgroundColor' => '#e3e3e3',
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
                        'height' => '24px',
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
                        'iconType' => 'youtube',
                        'link' => 'http://www.youtube.com',
                        'image' => $this->social_icon_url . '/06-full-symbol-color/Youtube.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Youtube',
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
                    'text' => '<p style="text-align: center;"><span style="color: #d52a2a;"><strong>Welcome to Vector Motors</strong></span></p>
    <p style="text-align: center; font-size: 11px;"><span style="color: #d52a2a;"><a href="[link:newsletter_view_in_browser_url]">' . __("View this in your browser.", 'mailpoet') . '</a></span></p>',
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
                        'iconType' => 'instagram',
                        'link' => 'http://instagram.com',
                        'image' => $this->social_icon_url . '/06-full-symbol-color/Instagram.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Instagram',
                       ],
                      1 =>
                       [
                        'type' => 'socialIcon',
                        'iconType' => 'email',
                        'link' => '',
                        'image' => $this->social_icon_url . '/06-full-symbol-color/Email.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Email',
                       ],
                      2 =>
                       [
                        'type' => 'socialIcon',
                        'iconType' => 'website',
                        'link' => '',
                        'image' => $this->social_icon_url . '/06-full-symbol-color/Website.png',
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
          2 =>
           [
            'type' => 'container',
            'columnLayout' => '2_1',
            'orientation' => 'horizontal',
            'image' =>
             [
              'src' => $this->template_image_url . '/Car-Header-2.jpg',
              'display' => 'scale',
             ],
            'styles' =>
             [
              'block' =>
               [
                'backgroundColor' => '#d52a2a',
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
                    'src' => $this->template_image_url . '/Car-Header-logo-1.png',
                    'alt' => 'Car-Header-logo-1',
                    'fullWidth' => false,
                    'width' => '167.5px',
                    'height' => '349px',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'textAlign' => 'left',
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
                        'height' => '118px',
                       ],
                     ],
                   ],
                  2 =>
                   [
                    'type' => 'text',
                    'text' => '<h3 style="font-size: 35px; line-height: 40px; text-align: left; border-left: 10px solid #d52a2a; margin-left: 20px; padding-left: 20px;"><strong><span style="color: #ffffff;">Welcome to the club</span></strong></h3>',
                   ],
                  3 =>
                   [
                    'type' => 'spacer',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => 'transparent',
                        'height' => '72px',
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
                    'type' => 'spacer',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#ffffff',
                        'height' => '20px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<h2 style="text-align: center;"><strong><span style="color: #d52a2a;">You\'ve bought the car,&nbsp;here\'s&nbsp;what happens next.</span></strong></h2>
    <p style="text-align: center;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam a elementum ex. Aliquam mollis metus ac nisl luctus pulvinar. Donec tincidunt pharetra sem, nec eleifend augue. Morbi id nunc commodo, tempor erat et, pretium neque. </span></p>
    <p style="text-align: center;"><span></span></p>
    <p style="text-align: center;"><span>Vivamus ante sapien, consequat vitae ante quis, facilisis pellentesque mi. Praesent at scelerisque leo. Donec elementum mi consequat, ultrices lorem nec, vestibulum arcu. Aenean id libero vitae felis consequat maximus.</span></p>',
                   ],
                  2 =>
                   [
                    'type' => 'spacer',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#e3e3e3',
                        'height' => '40px',
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
                'backgroundColor' => '#e3e3e3',
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
                    'text' => '<h1><strong>We\'re here to help</strong></h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam a elementum ex. Aliquam mollis metus ac nisl luctus pulvinar. Donec tincidunt pharetra sem, nec eleifend augue. Morbi id nunc commodo, tempor erat et, pretium neque.</p>
    <p></p>
    <p><strong><a href="">Get in touch with us here</a></strong></p>',
                   ],
                  1 =>
                   [
                    'type' => 'spacer',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#e3e3e3',
                        'height' => '113px',
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
                        'backgroundColor' => '#ffffff',
                        'height' => '42px',
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
                    'type' => 'button',
                    'text' => 'Servicing Plans',
                    'url' => '',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#d52a2a',
                        'borderColor' => '#0074a2',
                        'borderWidth' => '0px',
                        'borderRadius' => '0px',
                        'borderStyle' => 'solid',
                        'width' => '176px',
                        'lineHeight' => '50px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
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
                        'backgroundColor' => '#e3e3e3',
                        'height' => '30px',
                       ],
                     ],
                   ],
                  2 =>
                   [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Car-Wheel.jpg',
                    'alt' => 'Car-Wheel',
                    'fullWidth' => true,
                    'width' => '600px',
                    'height' => '560px',
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
                        'backgroundColor' => '#ffffff',
                        'height' => '20px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<h3 style="text-align: center;"><strong>We\'ll be in touch soon with updates</strong></h3>
    <p style="text-align: center;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam a elementum ex. Aliquam mollis metus ac nisl luctus pulvinar. Donec tincidunt pharetra sem, nec eleifend augue. Morbi id nunc commodo, tempor erat et, pretium neque.</span></p>',
                   ],
                  2 =>
                   [
                    'type' => 'spacer',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#ffffff',
                        'height' => '35px',
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
                'backgroundColor' => '#e3e3e3',
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
                        'backgroundColor' => '#e3e3e3',
                        'height' => '20px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'footer',
                    'text' => '<p><span style="color: #d52a2a;"><a href="[link:subscription_unsubscribe_url]" style="color: #d52a2a;">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]" style="color: #d52a2a;">' . __("Manage your subscription", 'mailpoet') . '</a></span><br />' . __("Add your postal address here!", 'mailpoet') . '</p>',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/Car-Footer.jpg',
                    'alt' => 'Car-Footer',
                    'fullWidth' => true,
                    'width' => '1280px',
                    'height' => '275px',
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
                    'type' => 'spacer',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#d52a2a',
                        'height' => '20px',
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
          'fontFamily' => 'Roboto',
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
          'fontColor' => '#d52a2a',
          'textDecoration' => 'underline',
         ],
        'wrapper' =>
         [
          'backgroundColor' => '#ffffff',
         ],
        'body' =>
         [
          'backgroundColor' => '#e3e3e3',
         ],
       ],
      'blockDefaults' =>
       [
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
          'text' => 'Servicing Plans',
          'url' => '',
          'styles' =>
           [
            'block' =>
             [
              'backgroundColor' => '#d52a2a',
              'borderColor' => '#0074a2',
              'borderWidth' => '0px',
              'borderRadius' => '0px',
              'borderStyle' => 'solid',
              'width' => '176px',
              'lineHeight' => '50px',
              'fontColor' => '#ffffff',
              'fontFamily' => 'Merriweather Sans',
              'fontSize' => '18px',
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
              'padding' => '18.5px',
              'borderStyle' => 'solid',
              'borderWidth' => '5px',
              'borderColor' => '#d52a2a',
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
        'social' =>
         [
          'iconSet' => 'full-symbol-color',
          'icons' =>
           [
            0 =>
             [
              'type' => 'socialIcon',
              'iconType' => 'instagram',
              'link' => 'http://instagram.com',
              'image' => $this->social_icon_url . '/06-full-symbol-color/Instagram.png',
              'height' => '32px',
              'width' => '32px',
              'text' => 'Instagram',
             ],
            1 =>
             [
              'type' => 'socialIcon',
              'iconType' => 'email',
              'link' => '',
              'image' => $this->social_icon_url . '/06-full-symbol-color/Email.png',
              'height' => '32px',
              'width' => '32px',
              'text' => 'Email',
             ],
            2 =>
             [
              'type' => 'socialIcon',
              'iconType' => 'website',
              'link' => '',
              'image' => $this->social_icon_url . '/06-full-symbol-color/Website.png',
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
              'height' => '118px',
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
              'fontFamily' => 'Merriweather Sans',
              'fontSize' => '12px',
              'textAlign' => 'center',
             ],
            'link' =>
             [
              'fontColor' => '#d52a2a',
              'textDecoration' => 'underline',
             ],
           ],
          'type' => 'header',
         ],
       ],
    ];
  }
}
