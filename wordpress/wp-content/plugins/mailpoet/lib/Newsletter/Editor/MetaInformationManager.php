<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Editor;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class MetaInformationManager {
  public function appendMetaInformation($content, $post, $args) {
    // Append author and categories above and below contents
    foreach (['above', 'below'] as $position) {
      $positionField = $position . 'Text';
      $text = [];

      if (isset($args['showAuthor']) && $args['showAuthor'] === $positionField) {
        $text[] = self::getPostAuthor(
          $post->post_author, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          $args['authorPrecededBy']
        );
      }

      if (isset($args['showCategories']) && $args['showCategories'] === $positionField) {
        $text[] = self::getPostCategories(
          $post->ID,
          $post->post_type, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          $args['categoriesPrecededBy']
        );
      }

      if (!empty($text)) {
        $text = '<p>' . implode('<br />', $text) . '</p>';
        if ($position === 'above') $content = $text . $content;
        else if ($position === 'below') $content .= $text;
      }
    }

    return $content;
  }

  private static function getPostCategories($postId, $postType, $precededBy) {
    $precededBy = trim($precededBy);

    // Get categories
    $categories = WPFunctions::get()->wpGetPostTerms(
      $postId,
      ['category'],
      ['fields' => 'names']
    );
    if (!empty($categories)) {
      // check if the user specified a label to be displayed before the author's name
      if (strlen($precededBy) > 0) {
        $content = stripslashes($precededBy) . ' ';
      } else {
        $content = '';
      }

      return $content . join(', ', $categories);
    } else {
      return '';
    }
  }

  private static function getPostAuthor($authorId, $precededBy) {
    $authorName = WPFunctions::get()->getTheAuthorMeta('display_name', (int)$authorId);

    $precededBy = trim($precededBy);
    if (strlen($precededBy) > 0) {
      $authorName = stripslashes($precededBy) . ' ' . $authorName;
    }

    return $authorName;
  }
}
