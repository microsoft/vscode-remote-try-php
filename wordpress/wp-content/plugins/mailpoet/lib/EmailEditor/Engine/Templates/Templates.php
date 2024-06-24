<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\EmailStylesSchema;
use WP_Block_Template;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
class Templates {
  const MAILPOET_EMAIL_META_THEME_TYPE = 'mailpoet_email_theme';
  const MAILPOET_TEMPLATE_EMPTY_THEME = ['version' => 2]; // The version 2 is important to merge themes correctly

  private Utils $utils;
  private string $pluginSlug = 'mailpoet/mailpoet';
  private string $postType = 'mailpoet_email';
  private string $templateDirectory;
  private array $templates = [];
  private array $themeJson = [];

  public function __construct(
    Utils $utils
  ) {
    $this->utils = $utils;
    $this->templateDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR;
  }

  public function initialize(): void {
    add_filter('pre_get_block_file_template', [$this, 'getBlockFileTemplate'], 10, 3);
    add_filter('get_block_templates', [$this, 'addBlockTemplates'], 10, 3);
    add_filter('theme_templates', [$this, 'addThemeTemplates'], 10, 4); // Needed when saving post – template association
    add_filter('get_block_template', [$this, 'addBlockTemplateDetails'], 10, 1);
    add_filter('rest_pre_insert_wp_template', [$this, 'forcePostContent'], 9, 1);
    $this->initializeTemplates();
    $this->initializeApi();
  }

  /**
   * Get a block template by ID.
   */
  public function getBlockTemplate($templateId) {
    $templates = $this->getBlockTemplates();
    return $templates[$templateId] ?? null;
  }

  /**
   * Get a predefined or user defined theme for a block template.
   *
   * @param string $templateId
   * @param int|null $templateWpId
   * @return array
   */
  public function getBlockTemplateTheme($templateId, $templateWpId = null) {
    // First check if there is a user updated theme saved
    $theme = $this->getCustomTemplateTheme($templateWpId);

    if ($theme) {
      return $theme;
    }

    // If there is no user edited theme, look for default template themes in files.
    ['prefix' => $templatePrefix, 'slug' => $templateSlug] = $this->utils->getTemplateIdParts($templateId);

    if ($this->pluginSlug !== $templatePrefix) {
      return self::MAILPOET_TEMPLATE_EMPTY_THEME;
    }

    if (!isset($this->themeJson[$templateSlug])) {
      $jsonFile = $this->templateDirectory . $templateSlug . '.json';

      if (file_exists($jsonFile)) {
        $this->themeJson[$templateSlug] = json_decode((string)file_get_contents($jsonFile), true);
      }
    }

    return $this->themeJson[$templateSlug] ?? self::MAILPOET_TEMPLATE_EMPTY_THEME;
  }

  public function getBlockFileTemplate($return, $templateId, $template_type) {
    ['prefix' => $templatePrefix, 'slug' => $templateSlug] = $this->utils->getTemplateIdParts($templateId);

    if ($this->pluginSlug !== $templatePrefix) {
        return $return;
    }

    $templatePath = $templateSlug . '.html';

    if (!is_readable($this->templateDirectory . $templatePath)) {
        return $return;
    }

    return $this->getBlockTemplateFromFile($templatePath);
  }

  public function addBlockTemplates($query_result, $query, $template_type) {
    if ('wp_template' !== $template_type) {
      return $query_result;
    }

    $post_type = isset($query['post_type']) ? $query['post_type'] : '';

    if ($post_type && $post_type !== $this->postType) {
      return $query_result;
    }

    foreach ($this->getBlockTemplates() as $blockTemplate) {
      $fits_slug_query = !isset($query['slug__in']) || in_array($blockTemplate->slug, $query['slug__in'], true);
      $fits_area_query = !isset($query['area']) || ( property_exists($blockTemplate, 'area') && $blockTemplate->area === $query['area'] );
      $should_include = $fits_slug_query && $fits_area_query;

      if ($should_include) {
          $query_result[] = $blockTemplate;
      }
    }

    return $query_result;
  }

  public function addThemeTemplates($templates, $theme, $post, $post_type) {
    if ($post_type && $post_type !== $this->postType) {
      return $templates;
    }
    foreach ($this->getBlockTemplates() as $blockTemplate) {
      $templates[$blockTemplate->slug] = $blockTemplate;
    }
    return $templates;
  }

  /**
   * This is a workaround to ensure the post object passed to `inject_ignored_hooked_blocks_metadata_attributes` contains
   * content to prevent the template being empty when saved. The issue currently occurs when WooCommerce enables block hooks,
   * and when older versions of `inject_ignored_hooked_blocks_metadata_attributes` are
   * used (before https://github.com/WordPress/WordPress/commit/725f302121c84c648c38789b2e88dbd1eb41fa48).
   * This can be removed in the future.
   *
   * To test the issue create a new email, revert template changes, save a color change, then save a color change again.
   * When you refresh if the post is blank, the issue is present.
   *
   * @param \stdClass $changes
   */
  public function forcePostContent($changes) {
    if (empty($changes->post_content) && !empty($changes->ID)) {
      // Find the existing post object.
      $post = get_post($changes->ID);
      if ($post && !empty($post->post_content)) {
        $changes->post_content = $post->post_content;
      }
    }
    return $changes;
  }

  /**
   * Add details to templates in editor.
   *
   * @param WP_Block_Template $block_template Block template object.
   * @return WP_Block_Template
   */
  public function addBlockTemplateDetails($block_template) {
    if (!$block_template || !isset($this->templates[$block_template->slug])) {
      return $block_template;
    }
    if (empty($block_template->title)) {
      $block_template->title = $this->templates[$block_template->slug]['title'];
    }
    if (empty($block_template->description)) {
      $block_template->description = $this->templates[$block_template->slug]['description'];
    }
    return $block_template;
  }

  /**
   * Initialize template details. This is done at runtime because of localisation.
   */
  private function initializeTemplates(): void {
    $this->templates['email-general'] = [
      'title' => __('General Email', 'mailpoet'),
      'description' => __('A general template for emails.', 'mailpoet'),
    ];
    $this->templates['awesome-one'] = [
      'title' => __('Awesome Template One', 'mailpoet'),
      'description' => __('A template used in testing.', 'mailpoet'),
    ];
    $this->templates['awesome-two'] = [
      'title' => __('Awesome Template Two', 'mailpoet'),
      'description' => __('A template used in testing.', 'mailpoet'),
    ];
    $this->templates['email-computing-mag'] = [
      'title' => __('Retro Computing Mag', 'mailpoet'),
      'description' => __('A retro themed template.', 'mailpoet'),
    ];
  }

  private function initializeApi(): void {
    register_post_meta(
      'wp_template',
      self::MAILPOET_EMAIL_META_THEME_TYPE,
      [
        'show_in_rest' => [
          'schema' => (new EmailStylesSchema())->getSchema(),
        ],
        'single' => true,
        'type' => 'object',
        'default' => self::MAILPOET_TEMPLATE_EMPTY_THEME,
      ]
    );
    register_rest_field(
      'wp_template',
      self::MAILPOET_EMAIL_META_THEME_TYPE,
      [
      'get_callback' => function($object) {
         return $this->getBlockTemplateTheme($object['id'], $object['wp_id']);
      },
      'update_callback' => function($value, $template) {
        return update_post_meta($template->wp_id, self::MAILPOET_EMAIL_META_THEME_TYPE, $value);
      },
      'schema' => (new EmailStylesSchema())->getSchema(),
      ]
    );
  }

  /**
   * Gets block templates indexed by ID.
   */
  private function getBlockTemplates() {
    $blockTemplates = array_map(function($templateSlug) {
      return $this->getBlockTemplateFromFile($templateSlug . '.html');
    }, array_keys($this->templates));
    $customTemplates = $this->getCustomTemplates(); // From the DB.
    $customTemplateIds = wp_list_pluck($customTemplates, 'id');

    // Combine to remove duplicates if a custom template has the same ID as a file template.
    return array_column(
      array_merge(
        $customTemplates,
        array_filter(
          $blockTemplates,
          function($blockTemplate) use ($customTemplateIds) {
              return !in_array($blockTemplate->id, $customTemplateIds, true);
          }
        ),
      ),
      null,
      'id'
    );
  }

  private function getBlockTemplateFromFile(string $template) {
    $template_slug = $this->utils->getBlockTemplateSlugFromPath($template);
    $templateObject = (object)[
      'slug' => $template_slug,
      'id' => $this->pluginSlug . '//' . $template_slug,
      'title' => $this->templates[$template_slug]['title'] ?? '',
      'description' => $this->templates[$template_slug]['description'] ?? '',
      'path' => $this->templateDirectory . $template,
      'type' => 'wp_template',
      'theme' => $this->pluginSlug,
      'source' => 'plugin',
      'post_types' => [
        $this->postType,
      ],
    ];
    return $this->utils->buildBlockTemplateFromFile($templateObject);
  }

  private function getCustomTemplates($slugs = [], $template_type = 'wp_template') {
    $check_query_args = [
        'post_type' => $template_type,
        'posts_per_page' => -1,
        'no_found_rows' => true,
        'tax_query' => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            [
                'taxonomy' => 'wp_theme',
                'field' => 'name',
                'terms' => [ $this->pluginSlug, get_stylesheet() ],
            ],
        ],
    ];

    if (is_array($slugs) && count($slugs) > 0) {
        $check_query_args['post_name__in'] = $slugs;
    }

    $check_query = new \WP_Query($check_query_args);
    $custom_templates = $check_query->posts;

    return array_map(
      function($custom_template) {
          return $this->utils->buildBlockTemplateFromPost($custom_template);
      },
      $custom_templates
    );
  }

  private function getCustomTemplateTheme($templateWpId) {
    if (!$templateWpId) {
      return null;
    }
    $theme = get_post_meta($templateWpId, self::MAILPOET_EMAIL_META_THEME_TYPE, true);
    if (is_array($theme) && isset($theme['styles'])) {
      return $theme;
    }
    return null;
  }
}
