<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class ModularStyleStories {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/modular-style-stories';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Modular Style Stories", 'mailpoet'),
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
                          'backgroundColor' => '#efe7f0',
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
                          'backgroundColor' => '#efe7f0',
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
                                  'src' => $this->template_image_url . '/Modular-Logo.png',
                                  'alt' => 'Modular-Logo',
                                  'fullWidth' => false,
                                  'width' => '271px',
                                  'height' => '37px',
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
                                          'height' => '26px',
                                         ],
                                     ],
                                 ],
                              1 =>
                                 [
                                  'type' => 'social',
                                  'iconSet' => 'full-symbol-color',
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
                                      3 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'pinterest',
                                          'link' => 'http://www.pinterest.com',
                                          'image' => $this->social_icon_url . '/06-full-symbol-color/Pinterest.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Pinterest',
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
                                          'backgroundColor' => '#efe7f0',
                                          'height' => '40px',
                                         ],
                                     ],
                                 ],
                             ],
                         ],
                     ],
                 ],
              3 =>
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
                  'titleFormat' => 'h3',
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
                      'type' => 'button',
                      'text' => 'Read more',
                      'url' => '[postLink]',
                      'styles' =>
                         [
                          'block' =>
                             [
                              'backgroundColor' => '#ffffff',
                              'borderColor' => '#ffffff',
                              'borderWidth' => '1px',
                              'borderRadius' => '0px',
                              'borderStyle' => 'solid',
                              'width' => '120px',
                              'lineHeight' => '40px',
                              'fontColor' => '#b956c5',
                              'fontFamily' => 'Verdana',
                              'fontSize' => '18px',
                              'fontWeight' => 'normal',
                              'textAlign' => 'center',
                             ],
                         ],
                      'context' => 'automatedLatestContentLayout.readMoreButton',
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
                              'borderStyle' => 'dashed',
                              'borderWidth' => '3px',
                              'borderColor' => '#efe7f0',
                             ],
                         ],
                      'context' => 'automatedLatestContentLayout.divider',
                     ],
                  'backgroundColor' => '#ffffff',
                  'backgroundColorAlternate' => '#eeeeee',
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
                                          'backgroundColor' => '#efe7f0',
                                          'height' => '40px',
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
                          'backgroundColor' => '#b956c5',
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
                                  'link' => 'http://mailpoet.info/ladybirds-transparent-shell-reveals-how-it-folds-its-wings/',
                                  'src' => $this->template_image_url . '/gettyimages-578313682-800x533.jpg',
                                  'alt' => 'Ladybird’s transparent shell reveals how it folds its wings',
                                  'fullWidth' => false,
                                  'width' => 660,
                                  'height' => 440,
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
                                  'text' => '<h3 style="text-align: left;"><strong>Ladybird&rsquo;s transparent shell reveals how it folds its wings</strong></h3>
<p class="mailpoet_wp_post">They certainly know how to fold. A see-through artificial wing case has been used to watch for the first time as ladybirds put away their wings after flight.</p>',
                                 ],
                              2 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Read More',
                                  'url' => 'http://mailpoet.info/ladybirds-transparent-shell-reveals-how-it-folds-its-wings/',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#b956c5',
                                          'borderColor' => '#000000',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '0px',
                                          'borderStyle' => 'solid',
                                          'width' => '103px',
                                          'lineHeight' => '34px',
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
                                  'link' => 'http://mailpoet.info/plasma-jet-engines-that-could-take-you-from-the-ground-to-space/',
                                  'src' => $this->template_image_url . '/plasma-stingray111-800x533.jpg',
                                  'alt' => 'Plasma jet engines that could take you from the ground to space',
                                  'fullWidth' => false,
                                  'width' => 660,
                                  'height' => 440,
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
                                  'text' => '<h3 style="text-align: left;"><strong>Plasma jet engines that could take you from ground to space</strong></h3>
<p class="mailpoet_wp_post">FORGET fuel-powered jet engines. We&rsquo;re on the verge of having aircraft that can fly from the ground up to the edge of space using air and electricity alone.</p>',
                                 ],
                              2 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Read More',
                                  'url' => 'http://mailpoet.info/plasma-jet-engines-that-could-take-you-from-the-ground-to-space/',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#b956c5',
                                          'borderColor' => '#000000',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '0px',
                                          'borderStyle' => 'solid',
                                          'width' => '103px',
                                          'lineHeight' => '34px',
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
                          'backgroundColor' => '#efe7f0',
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
                          'backgroundColor' => '#efe7f0',
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
                                  'link' => 'http://mailpoet.info/cutting-through-the-smog-what-to-do-to-fight-air-pollution/',
                                  'src' => $this->template_image_url . '/5_what_to_do_p352m1141746-800x533.jpg',
                                  'alt' => 'Cutting through the smog: What to do to fight air pollution',
                                  'fullWidth' => false,
                                  'width' => 660,
                                  'height' => 440,
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
                                  'text' => '<h3 style="text-align: left;"><span style="color: #333333;"><strong>Cutting through the smog: What to do to fight air pollution</strong></span></h3>',
                                 ],
                              2 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Read More',
                                  'url' => 'http://mailpoet.info/cutting-through-the-smog-what-to-do-to-fight-air-pollution/',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#b956c5',
                                          'borderColor' => '#000000',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '0px',
                                          'borderStyle' => 'solid',
                                          'width' => '103px',
                                          'lineHeight' => '34px',
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
                                  'link' => 'http://mailpoet.info/ladybirds-transparent-shell-reveals-how-it-folds-its-wings/',
                                  'src' => $this->template_image_url . '/gettyimages-578313682-800x533.jpg',
                                  'alt' => 'Ladybird’s transparent shell reveals how it folds its wings',
                                  'fullWidth' => false,
                                  'width' => 660,
                                  'height' => 440,
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
                                  'text' => '<h3 style="text-align: left;"><span style="color: #333333;"><strong>Ladybird&rsquo;s transparent shell reveals how it folds its wings</strong></span></h3>',
                                 ],
                              2 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Read More',
                                  'url' => 'http://mailpoet.info/ladybirds-transparent-shell-reveals-how-it-folds-its-wings/',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#b956c5',
                                          'borderColor' => '#000000',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '0px',
                                          'borderStyle' => 'solid',
                                          'width' => '103px',
                                          'lineHeight' => '34px',
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
                                  'type' => 'image',
                                  'link' => 'http://mailpoet.info/plasma-jet-engines-that-could-take-you-from-the-ground-to-space/',
                                  'src' => $this->template_image_url . '/plasma-stingray111-800x533.jpg',
                                  'alt' => 'Plasma jet engines that could take you from the ground to space',
                                  'fullWidth' => false,
                                  'width' => 660,
                                  'height' => 440,
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
                                  'text' => '<h3 style="text-align: left;"><span style="color: #333333;"><strong>Plasma jet engines that could take you from the ground to space</strong></span></h3>',
                                 ],
                              2 =>
                                 [
                                  'type' => 'button',
                                  'text' => 'Read More',
                                  'url' => 'http://mailpoet.info/plasma-jet-engines-that-could-take-you-from-the-ground-to-space/',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#b956c5',
                                          'borderColor' => '#000000',
                                          'borderWidth' => '0px',
                                          'borderRadius' => '0px',
                                          'borderStyle' => 'solid',
                                          'width' => '103px',
                                          'lineHeight' => '34px',
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
                          'backgroundColor' => '#efe7f0',
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
                          'backgroundColor' => '#b956c5',
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
                                          'height' => '21px',
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
                              2 =>
                                 [
                                  'type' => 'footer',
                                  'text' => '<p><span style="color: #ffffff;"><a href="[link:subscription_unsubscribe_url]" style="color: #ffffff;">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]" style="color: #ffffff;">' . __("Manage your subscription", 'mailpoet') . '</a></span><br /><span style="color: #ffffff;">' . __("Add your postal address here!", 'mailpoet') . '</span></p>',
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
             ],
         ],
      'globalStyles' =>
         [
          'text' =>
             [
              'fontColor' => '#ffffff',
              'fontFamily' => 'Arial',
              'fontSize' => '14px',
             ],
          'h1' =>
             [
              'fontColor' => '#ffffff',
              'fontFamily' => 'Arial',
              'fontSize' => '30px',
             ],
          'h2' =>
             [
              'fontColor' => '#ffffff',
              'fontFamily' => 'Arial',
              'fontSize' => '26px',
             ],
          'h3' =>
             [
              'fontColor' => '#ffffff',
              'fontFamily' => 'Arial',
              'fontSize' => '20px',
             ],
          'link' =>
             [
              'fontColor' => '#ffffff',
              'textDecoration' => 'underline',
             ],
          'wrapper' =>
             [
              'backgroundColor' => '#b956c5',
             ],
          'body' =>
             [
              'backgroundColor' => '#efe7f0',
             ],
         ],
      'blockDefaults' =>
         [
          'automatedLatestContent' =>
             [
              'amount' => '2',
              'contentType' => 'post',
              'inclusionType' => 'include',
              'displayType' => 'excerpt',
              'titleFormat' => 'h2',
              'titleAlignment' => 'left',
              'titleIsLink' => false,
              'imageFullWidth' => true,
              'featuredImagePosition' => 'aboveTitle',
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
                          'backgroundColor' => '#ffffff',
                          'borderColor' => '#0074a2',
                          'borderWidth' => '0px',
                          'borderRadius' => '0px',
                          'borderStyle' => 'solid',
                          'width' => '116px',
                          'lineHeight' => '40px',
                          'fontColor' => '#b956c5',
                          'fontFamily' => 'Arial',
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
                  'context' => 'automatedLatestContent.divider',
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => 'transparent',
                          'padding' => '13px',
                          'borderStyle' => 'dashed',
                          'borderWidth' => '3px',
                          'borderColor' => '#ffffff',
                         ],
                     ],
                  'type' => 'divider',
                 ],
              'backgroundColor' => '#ffffff',
              'backgroundColorAlternate' => '#eeeeee',
              'type' => 'automatedLatestContent',
              'terms' =>
                 [
                 ],
              'withLayout' => false,
             ],
          'automatedLatestContentLayout' =>
             [
              'amount' => '3',
              'withLayout' => true,
              'contentType' => 'post',
              'inclusionType' => 'include',
              'displayType' => 'excerpt',
              'titleFormat' => 'h3',
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
                          'backgroundColor' => '#ffffff',
                          'borderColor' => '#ffffff',
                          'borderWidth' => '1px',
                          'borderRadius' => '0px',
                          'borderStyle' => 'solid',
                          'width' => '120px',
                          'lineHeight' => '40px',
                          'fontColor' => '#b956c5',
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
                  'context' => 'automatedLatestContentLayout.divider',
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => 'transparent',
                          'padding' => '13px',
                          'borderStyle' => 'dashed',
                          'borderWidth' => '3px',
                          'borderColor' => '#efe7f0',
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
          'container' =>
             [
              'styles' =>
                 [
                  'block' =>
                     [
                      'backgroundColor' => 'transparent',
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
              'imageFullWidth' => true,
              'featuredImagePosition' => 'aboveTitle',
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
                  'context' => 'posts.readMoreButton',
                  'styles' =>
                     [
                      'block' =>
                         [
                          'backgroundColor' => '#ffffff',
                          'borderColor' => '#0074a2',
                          'borderWidth' => '1px',
                          'borderRadius' => '0px',
                          'borderStyle' => 'solid',
                          'width' => '180px',
                          'lineHeight' => '40px',
                          'fontColor' => '#ffffff',
                          'fontFamily' => 'Arial',
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
                      'backgroundColor' => '#efe7f0',
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
