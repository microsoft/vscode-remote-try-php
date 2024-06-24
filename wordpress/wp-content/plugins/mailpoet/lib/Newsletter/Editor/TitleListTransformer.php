<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Editor;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class TitleListTransformer {

  private $args;

  public function __construct(
    $args
  ) {
    $this->args = $args;
  }

  public function transform($posts) {
    $results = array_map(function($post) {
      return $this->getPostTitle($post);
    }, $posts);

    return [
      $this->wrap([
        'type' => 'text',
        'text' => '<ul>' . implode('', $results) . '</ul>',
      ])];
  }

  private function wrap($block) {
    return LayoutHelper::row([
      LayoutHelper::col([$block]),
    ]);
  }

  private function getPostTitle($post) {
    $title = $post->post_title; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $alignment = $this->args['titleAlignment'];
    $alignment = (in_array($alignment, ['left', 'right', 'center'])) ? $alignment : 'left';

    if ($this->args['titleIsLink']) {
      $title = '<a data-post-id="' . $post->ID . '" href="' . WPFunctions::get()->getPermalink($post->ID) . '">' . $title . '</a>';
    }

    return '<li style="text-align: ' . $alignment . ';">' . $title . '</li>';
  }
}
