<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class ClearNews {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/clear-news';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Clear News", 'mailpoet'),
      'categories' => json_encode(['notification', 'all']),
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
                        'backgroundColor' => '#ffffff',
                        'height' => '27px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'header',
                    'text' => '<p><a href="[link:newsletter_view_in_browser_url]">' . __("View this in your browser.", 'mailpoet') . '</a></p>',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#ffffff',
                       ],
                      'text' =>
                       [
                        'fontColor' => '#222222',
                        'fontFamily' => 'Arial',
                        'fontSize' => '11px',
                        'textAlign' => 'left',
                       ],
                      'link' =>
                       [
                        'fontColor' => '#e2973f',
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
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/News-Logo-1.png',
                    'alt' => 'News-Logo',
                    'fullWidth' => false,
                    'width' => '120px',
                    'height' => '167px',
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
                        'backgroundColor' => '#ffffff',
                        'height' => '30px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<h3 style="text-align: right;"><span style="color: #808080;"><strong>October 2018</strong></span></h3>',
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
                    'text' => '<h1 style="text-align: left; line-height: 1.3;"><strong>Good Morning!</strong></h1>
    <h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce egestas nisl vel ante finibus fringilla ullamcorper non lectus. Aenean leo neque, egestas et lacus eu, viverra luctus nisi. Donec dapibus mauris at fringilla consequat. Cras sed porta nunc. Ut tincidunt luctus felis sed suscipit. Sed tristique faucibus fermentum.</h3>',
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
                        'backgroundColor' => '#ffffff',
                        'height' => '24px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'image',
                    'link' => '',
                    'src' => $this->template_image_url . '/UEl2.gif',
                    'alt' => 'UEl2',
                    'fullWidth' => false,
                    'width' => '360px',
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
                    'type' => 'divider',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => 'transparent',
                        'padding' => '13px',
                        'borderStyle' => 'dashed',
                        'borderWidth' => '2px',
                        'borderColor' => '#e2973f',
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
                        'backgroundColor' => '#ffffff',
                        'height' => '20px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<h2><strong>Today\'s Top Stories</strong></h2>',
                   ],
                 ],
               ],
             ],
           ],
          5 =>
           [
            'type' => 'automatedLatestContentLayout',
            'withLayout' => true,
            'amount' => '3',
            'contentType' => 'post',
            'terms' =>
             [
             ],
            'inclusionType' => 'include',
            'displayType' => 'excerpt',
            'titleFormat' => 'h2',
            'titleAlignment' => 'left',
            'titleIsLink' => false,
            'imageFullWidth' => true,
            'featuredImagePosition' => 'left',
            'showAuthor' => 'no',
            'authorPrecededBy' => 'Author:',
            'showCategories' => 'no',
            'categoriesPrecededBy' => 'Categories:',
            'readMoreType' => 'button',
            'readMoreText' => 'Read more',
            'readMoreButton' =>
             [
              'type' => 'button',
              'text' => 'Read more',
              'url' => '[postLink]',
              'styles' =>
               [
                'block' =>
                 [
                  'backgroundColor' => '#e2973f',
                  'borderColor' => '#e2973f',
                  'borderWidth' => '0px',
                  'borderRadius' => '5px',
                  'borderStyle' => 'solid',
                  'width' => '110px',
                  'lineHeight' => '40px',
                  'fontColor' => '#ffffff',
                  'fontFamily' => 'Arial',
                  'fontSize' => '14px',
                  'fontWeight' => 'bold',
                  'textAlign' => 'left',
                 ],
               ],
              'context' => 'automatedLatestContentLayout.readMoreButton',
             ],
            'sortBy' => 'newest',
            'showDivider' => false,
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
              'context' => 'automatedLatestContentLayout.divider',
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
                    'type' => 'divider',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => 'transparent',
                        'padding' => '13px',
                        'borderStyle' => 'dashed',
                        'borderWidth' => '2px',
                        'borderColor' => '#e2973f',
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
                    'text' => '<h2><strong>We cover all types of news</strong></h2>',
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
                    'type' => 'button',
                    'text' => 'World',
                    'url' => '[postLink]',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#e23f3f',
                        'borderColor' => '#e2973f',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '288px',
                        'lineHeight' => '50px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '20px',
                        'fontWeight' => 'bold',
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
                    'type' => 'button',
                    'text' => 'Business',
                    'url' => '[postLink]',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#50b6ce',
                        'borderColor' => '#e2973f',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '288px',
                        'lineHeight' => '50px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '20px',
                        'fontWeight' => 'bold',
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
                    'text' => 'Politics',
                    'url' => '[postLink]',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#506dce',
                        'borderColor' => '#e2973f',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '288px',
                        'lineHeight' => '50px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '20px',
                        'fontWeight' => 'bold',
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
                    'type' => 'button',
                    'text' => 'Sports',
                    'url' => '[postLink]',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#e1bc2d',
                        'borderColor' => '#e2973f',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '288px',
                        'lineHeight' => '50px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '20px',
                        'fontWeight' => 'bold',
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
                    'type' => 'button',
                    'text' => 'Science',
                    'url' => '[postLink]',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#a650ce',
                        'borderColor' => '#e2973f',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '288px',
                        'lineHeight' => '50px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '20px',
                        'fontWeight' => 'bold',
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
                    'text' => 'Health',
                    'url' => '[postLink]',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#64b03c',
                        'borderColor' => '#e2973f',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '288px',
                        'lineHeight' => '50px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '20px',
                        'fontWeight' => 'bold',
                        'textAlign' => 'center',
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
                    'type' => 'button',
                    'text' => 'Family',
                    'url' => '[postLink]',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#278f6e',
                        'borderColor' => '#e2973f',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '288px',
                        'lineHeight' => '50px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '20px',
                        'fontWeight' => 'bold',
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
                    'type' => 'button',
                    'text' => 'Arts',
                    'url' => '[postLink]',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#7c5e5e',
                        'borderColor' => '#e2973f',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '288px',
                        'lineHeight' => '50px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '20px',
                        'fontWeight' => 'bold',
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
                    'text' => 'Local',
                    'url' => '[postLink]',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#4d4d4d',
                        'borderColor' => '#e2973f',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '288px',
                        'lineHeight' => '50px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Merriweather Sans',
                        'fontSize' => '20px',
                        'fontWeight' => 'bold',
                        'textAlign' => 'center',
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
                 ],
               ],
             ],
           ],
          12 =>
           [
            'type' => 'container',
            'columnLayout' => '1_2',
            'orientation' => 'horizontal',
            'image' =>
             [
              'src' => $this->template_image_url . '/News-Crossword.jpg',
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
                        'height' => '60px',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<h2 style="text-align: right;"><span style="color: #ffffff;">The Friday Crossword</span></h2>
    <p style="text-align: right;"><span style="color: #ffffff;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vitae ornare elit. Duis laoreet justo sed fringilla maximus. Aenean pharetra nec risus a vestibulum.</span></span></p>',
                   ],
                  2 =>
                   [
                    'type' => 'button',
                    'text' => 'Get started',
                    'url' => '[postLink]',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => '#e2973f',
                        'borderColor' => '#e2973f',
                        'borderWidth' => '0px',
                        'borderRadius' => '5px',
                        'borderStyle' => 'solid',
                        'width' => '110px',
                        'lineHeight' => '40px',
                        'fontColor' => '#ffffff',
                        'fontFamily' => 'Arial',
                        'fontSize' => '14px',
                        'fontWeight' => 'bold',
                        'textAlign' => 'right',
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
                        'height' => '22px',
                       ],
                     ],
                   ],
                 ],
               ],
             ],
           ],
          13 =>
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
                    'type' => 'divider',
                    'styles' =>
                     [
                      'block' =>
                       [
                        'backgroundColor' => 'transparent',
                        'padding' => '34.5px',
                        'borderStyle' => 'dashed',
                        'borderWidth' => '2px',
                        'borderColor' => '#e2973f',
                       ],
                     ],
                   ],
                 ],
               ],
             ],
           ],
          14 =>
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
                    'src' => $this->template_image_url . '/News-Logo-1.png',
                    'alt' => 'News-Logo',
                    'fullWidth' => false,
                    'width' => '120px',
                    'height' => '167px',
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
                    'type' => 'text',
                    'text' => '<p style="text-align: center;"><strong><a href="[link:subscription_unsubscribe_url]">' . __("Unsubscribe", 'mailpoet') . '</a></strong></p>
    <p style="text-align: center;"><strong><a href="[link:subscription_manage_url]">' . __("Manage your subscription", 'mailpoet') . '</a></strong></p>',
                   ],
                  1 =>
                   [
                    'type' => 'text',
                    'text' => '<p style="text-align: center;">Add your postal address!</p>',
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
                        'iconType' => 'youtube',
                        'link' => 'http://www.youtube.com',
                        'image' => $this->social_icon_url . '/03-circles/Youtube.png',
                        'height' => '32px',
                        'width' => '32px',
                        'text' => 'Youtube',
                       ],
                     ],
                   ],
                  1 =>
                   [
                    'type' => 'social',
                    'iconSet' => 'default',
                    'icons' =>
                     [
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
          'fontSize' => '15px',
         ],
        'h1' =>
         [
          'fontColor' => '#111111',
          'fontFamily' => 'Merriweather Sans',
          'fontSize' => '40px',
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
          'fontSize' => '16px',
         ],
        'link' =>
         [
          'fontColor' => '#e2973f',
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
          'amount' => '3',
          'withLayout' => true,
          'contentType' => 'post',
          'inclusionType' => 'include',
          'displayType' => 'excerpt',
          'titleFormat' => 'h2',
          'titleAlignment' => 'left',
          'titleIsLink' => false,
          'imageFullWidth' => true,
          'featuredImagePosition' => 'left',
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
                'backgroundColor' => '#e2973f',
                'borderColor' => '#e2973f',
                'borderWidth' => '0px',
                'borderRadius' => '5px',
                'borderStyle' => 'solid',
                'width' => '110px',
                'lineHeight' => '40px',
                'fontColor' => '#ffffff',
                'fontFamily' => 'Arial',
                'fontSize' => '14px',
                'fontWeight' => 'bold',
                'textAlign' => 'left',
               ],
             ],
            'type' => 'button',
           ],
          'sortBy' => 'newest',
          'showDivider' => false,
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
            'type' => 'divider',
           ],
          'backgroundColor' => '#ffffff',
          'backgroundColorAlternate' => '#eeeeee',
          'type' => 'automatedLatestContentLayout',
          'terms' =>
           [
           ],
         ],
        'button' =>
         [
          'text' => 'Read more',
          'url' => '[postLink]',
          'styles' =>
           [
            'block' =>
             [
              'backgroundColor' => '#4d4d4d',
              'borderColor' => '#e2973f',
              'borderWidth' => '0px',
              'borderRadius' => '5px',
              'borderStyle' => 'solid',
              'width' => '288px',
              'lineHeight' => '50px',
              'fontColor' => '#ffffff',
              'fontFamily' => 'Merriweather Sans',
              'fontSize' => '20px',
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
              'padding' => '34.5px',
              'borderStyle' => 'dashed',
              'borderWidth' => '2px',
              'borderColor' => '#e2973f',
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
              'fontSize' => '13px',
              'textAlign' => 'right',
             ],
            'link' =>
             [
              'fontColor' => '#e2973f',
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
          'displayType' => 'titleOnly',
          'titleFormat' => 'h2',
          'titleAlignment' => 'left',
          'titleIsLink' => false,
          'imageFullWidth' => true,
          'featuredImagePosition' => 'centered',
          'showAuthor' => 'no',
          'authorPrecededBy' => 'Author:',
          'showCategories' => 'no',
          'categoriesPrecededBy' => 'Categories:',
          'readMoreType' => 'button',
          'readMoreText' => 'Read more',
          'readMoreButton' =>
           [
            'text' => 'Read more',
            'url' => 'http://mailpoet.info/ladybirds-transparent-shell-reveals-how-it-folds-its-wings/',
            'context' => 'posts.readMoreButton',
            'styles' =>
             [
              'block' =>
               [
                'backgroundColor' => '#e2973f',
                'borderColor' => '#e2973f',
                'borderWidth' => '0px',
                'borderRadius' => '40px',
                'borderStyle' => 'solid',
                'width' => '110px',
                'lineHeight' => '40px',
                'fontColor' => '#ffffff',
                'fontFamily' => 'Arial',
                'fontSize' => '14px',
                'fontWeight' => 'bold',
                'textAlign' => 'left',
               ],
             ],
            'type' => 'button',
           ],
          'sortBy' => 'newest',
          'showDivider' => false,
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
              'iconType' => 'youtube',
              'link' => 'http://www.youtube.com',
              'image' => $this->social_icon_url . '/03-circles/Youtube.png',
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
              'backgroundColor' => '#ffffff',
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
              'backgroundColor' => '#ffffff',
             ],
            'text' =>
             [
              'fontColor' => '#222222',
              'fontFamily' => 'Arial',
              'fontSize' => '11px',
              'textAlign' => 'left',
             ],
            'link' =>
             [
              'fontColor' => '#e2973f',
              'textDecoration' => 'underline',
             ],
           ],
          'type' => 'header',
         ],
       ],
    ];
  }
}
