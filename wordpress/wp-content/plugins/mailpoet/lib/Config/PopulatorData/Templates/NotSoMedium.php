<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class NotSoMedium {

  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/not-so-medium';
    $this->social_icon_url = $assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("One Full Post In An Email", 'mailpoet'),
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
                                  'src' => $this->template_image_url . '/Not-So-Medium-Logo.png',
                                  'alt' => 'Not-So-Medium-Logo',
                                  'fullWidth' => false,
                                  'width' => '210px',
                                  'height' => '90px',
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
                                          'height' => '40px',
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
                                          'iconType' => 'website',
                                          'link' => '',
                                          'image' => $this->social_icon_url . '/07-full-symbol-black/Website.png',
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
                          'backgroundColor' => '#ebebeb',
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
                                  'text' => '<p><strong><em>Welcome to this week\'s post. </em></strong></p>
<p><em>Every Friday, we send you the most interesting story&nbsp;of the week in full.</em></p>',
                                 ],
                              2 =>
                                 [
                                  'type' => 'spacer',
                                  'styles' =>
                                     [
                                      'block' =>
                                         [
                                          'backgroundColor' => '#d6d6d6',
                                          'height' => '20px',
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
                                          'backgroundColor' => '#ffffff',
                                          'height' => '63px',
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
                  'amount' => '1',
                  'contentType' => 'post',
                  'terms' =>
                     [
                     ],
                  'inclusionType' => 'include',
                  'displayType' => 'full',
                  'titleFormat' => 'h1',
                  'titleAlignment' => 'center',
                  'titleIsLink' => true,
                  'imageFullWidth' => false,
                  'featuredImagePosition' => 'alternate',
                  'showAuthor' => 'aboveText',
                  'authorPrecededBy' => 'Author:',
                  'showCategories' => 'aboveText',
                  'categoriesPrecededBy' => 'Categories:',
                  'readMoreType' => 'link',
                  'readMoreText' => 'View this post online & share!',
                  'readMoreButton' =>
                     [
                      'type' => 'button',
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
                              'borderStyle' => 'solid',
                              'borderWidth' => '1px',
                              'borderColor' => '#aaaaaa',
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
                          'backgroundColor' => '#ebebeb',
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
                                  'text' => '<p style="text-align: center;"><strong><em>Share this with your friends</em></strong></p>
<p style="text-align: center;"><em>We promise not to spam anyone, and we only send our great articles to any email addresses in our list. Promise!</em></p>',
                                 ],
                              2 =>
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
                                          'iconType' => 'google-plus',
                                          'link' => 'http://plus.google.com',
                                          'image' => $this->social_icon_url . '/03-circles/Google-Plus.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Google Plus',
                                         ],
                                      3 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'linkedin',
                                          'link' => 'http://www.linkedin.com',
                                          'image' => $this->social_icon_url . '/03-circles/LinkedIn.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'LinkedIn',
                                         ],
                                      4 =>
                                         [
                                          'type' => 'socialIcon',
                                          'iconType' => 'email',
                                          'link' => '',
                                          'image' => $this->social_icon_url . '/03-circles/Email.png',
                                          'height' => '32px',
                                          'width' => '32px',
                                          'text' => 'Email',
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
                                          'backgroundColor' => '#d6d6d6',
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
                                          'height' => '20px',
                                         ],
                                     ],
                                 ],
                              1 =>
                                 [
                                  'type' => 'footer',
                                  'text' => '<p><strong><a href="[link:subscription_unsubscribe_url]">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]">' . __("Manage your subscription", 'mailpoet') . '</a></strong><br />' . __("Add your postal address here!", 'mailpoet') . '</p>',
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
                                          'fontColor' => '#2c2c2c',
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
              'fontFamily' => 'Georgia',
              'fontSize' => '18px',
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
              'amount' => '1',
              'withLayout' => true,
              'contentType' => 'post',
              'inclusionType' => 'include',
              'displayType' => 'full',
              'titleFormat' => 'h1',
              'titleAlignment' => 'center',
              'titleIsLink' => true,
              'imageFullWidth' => false,
              'featuredImagePosition' => 'alternate',
              'showAuthor' => 'aboveText',
              'authorPrecededBy' => 'Author:',
              'showCategories' => 'aboveText',
              'categoriesPrecededBy' => 'Categories:',
              'readMoreType' => 'link',
              'readMoreText' => 'View this post online & share!',
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
                          'borderStyle' => 'solid',
                          'borderWidth' => '1px',
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
                      'backgroundColor' => '#ffffff',
                      'height' => '63px',
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
