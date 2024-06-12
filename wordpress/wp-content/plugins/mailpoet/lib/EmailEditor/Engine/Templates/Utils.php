<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Templates;

if (!defined('ABSPATH')) exit;


use WP_Block_Template;
use WP_Error;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
class Utils {
  /**
   * Gets the prefix and slug from the template ID.
   *
   * @param string $templateId Id of the template in prefix//slug format.
   * @return array Associative array with keys 'prefix' and 'slug'.
   */
  public function getTemplateIdParts($templateId) {
    $template_name_parts = explode('//', $templateId);

    if (count($template_name_parts) < 2) {
      return [
        'prefix' => '',
        'slug' => '',
      ];
    }

    return [
      'prefix' => $template_name_parts[0],
      'slug' => $template_name_parts[1],
    ];
  }

  public static function getBlockTemplateSlugFromPath($path) {
    return basename($path, '.html');
  }

  public function buildBlockTemplateFromPost($post) {
    $terms = get_the_terms($post, 'wp_theme');

    if (is_wp_error($terms)) {
        return $terms;
    }

    if (!$terms) {
      return new WP_Error('template_missing_theme', 'No theme is defined for this template.');
    }

    $templatePrefix = $terms[0]->name;
    $templateSlug = $post->post_name;
    $templateId = $templatePrefix . '//' . $templateSlug;

    $template = new WP_Block_Template();
    $template->wp_id = $post->ID;
    $template->id = $templateId;
    $template->theme = $templatePrefix;
    $template->content = $post->post_content ? $post->post_content : '<p>empty</p>';
    $template->slug = $templateSlug;
    $template->source = 'custom';
    $template->type = $post->post_type;
    $template->description = $post->post_excerpt;
    $template->title = $post->post_title;
    $template->status = $post->post_status;
    $template->has_theme_file = false;
    $template->is_custom = true;
    $template->post_types = [];

    if ('wp_template_part' === $post->post_type) {
      $type_terms = get_the_terms($post, 'wp_template_part_area');

      if (!is_wp_error($type_terms) && false !== $type_terms) {
        $template->area = $type_terms[0]->name;
      }
    }

    return $template;
  }

  public function buildBlockTemplateFromFile($templateObject): WP_Block_Template {
    $template = new WP_Block_Template();
    $template->id = $templateObject->id;
    $template->theme = $templateObject->theme;
    $template->content = (string)file_get_contents($templateObject->path);
    $template->source = $templateObject->source;
    $template->slug = $templateObject->slug;
    $template->type = $templateObject->type;
    $template->title = $templateObject->title;
    $template->description = $templateObject->description;
    $template->status = 'publish';
    $template->has_theme_file = false;
    $template->post_types = $templateObject->post_types;
    $template->is_custom = false; // Templates are only custom if they are loaded from the DB.
    $template->area = 'uncategorized';
    return $template;
  }
}
