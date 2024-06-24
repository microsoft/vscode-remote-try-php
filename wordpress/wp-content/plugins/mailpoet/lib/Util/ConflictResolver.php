<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class ConflictResolver {
  public $permittedAssetsLocations = [
    'styles' => [
      'mailpoet',
      // WP default
      '^/wp-admin',
      '^/wp-includes',
      // CDN
      'googleapis.com/ajax/libs',
      'wp.com',
      // third-party
      'jetpack',
      'query-monitor',
      'wpt-tx-updater-network',
      // WP.com
      '^/_static',
      'mu-host-plugins/debug-bar/css',
      'woocommerce-payments/',
      'automatewoo/',
      'full-site-editing',
      'wpcomsh',
      // Gutenberg
      'gutenberg/',
    ],
    'scripts' => [
      'mailpoet',
      // WP default
      '^/wp-admin',
      '^/wp-includes',
      // CDN
      'googleapis.com/ajax/libs',
      'wp.com',
      // third-party
      'query-monitor',
      'wpt-tx-updater-network',
      // WP.com
      'full-site-editing',
      'wpcomsh',
    ],
  ];

  public function init() {
    WPFunctions::get()->addAction(
      'mailpoet_conflict_resolver_router_url_query_parameters',
      [
        $this,
        'resolveRouterUrlQueryParametersConflict',
      ]
    );
    WPFunctions::get()->addAction(
      'mailpoet_conflict_resolver_styles',
      [
        $this,
        'resolveStylesConflict',
      ]
    );
    WPFunctions::get()->addAction(
      'mailpoet_conflict_resolver_scripts',
      [
        $this,
        'resolveScriptsConflict',
      ]
    );
    WPFunctions::get()->addAction(
      'mailpoet_conflict_resolver_scripts',
      [
        $this,
        'resolveEditorConflict',
      ]
    );
    WPFunctions::get()->addAction(
      'mailpoet_conflict_resolver_scripts',
      [
        $this,
        'resolveTinyMceConflict',
      ]
    );
  }

  public function resolveRouterUrlQueryParametersConflict() {
    // prevents other plugins from overtaking URL query parameters 'action=' and 'endpoint='
    unset($_GET['endpoint'], $_GET['action']);
  }

  public function resolveStylesConflict() {
    $_this = $this;
    $_this->permittedAssetsLocations['styles'] = WPFunctions::get()->applyFilters('mailpoet_conflict_resolver_whitelist_style', $_this->permittedAssetsLocations['styles']);
    // unload all styles except from the list of allowed
    $dequeueStyles = function() use($_this) {
      global $wp_styles; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      if (!isset($wp_styles->registered)) return; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      if (empty($wp_styles->queue)) return; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      foreach ($wp_styles->queue as $wpStyle) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        if (empty($wp_styles->registered[$wpStyle])) continue; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $registeredStyle = $wp_styles->registered[$wpStyle]; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        if (!is_string($registeredStyle->src)) {
          continue;
        }
        if (!preg_match('!' . implode('|', $_this->permittedAssetsLocations['styles']) . '!i', $registeredStyle->src)) {
          WPFunctions::get()->wpDequeueStyle($wpStyle);
        }
      }
    };

    // execute last in the following hooks
    $executeLast = PHP_INT_MAX;
    WPFunctions::get()->addAction('admin_enqueue_scripts', $dequeueStyles, $executeLast); // used also for styles
    WPFunctions::get()->addAction('admin_footer', $dequeueStyles, $executeLast);

    // execute first in hooks for printing (after printing is too late)
    $executeFirst = defined('PHP_INT_MIN') ? constant('PHP_INT_MIN') : ~PHP_INT_MAX;
    WPFunctions::get()->addAction('admin_print_styles', $dequeueStyles, $executeFirst);
    WPFunctions::get()->addAction('admin_print_footer_scripts', $dequeueStyles, $executeFirst);
  }

  public function resolveScriptsConflict() {
    $_this = $this;
    $_this->permittedAssetsLocations['scripts'] = WPFunctions::get()->applyFilters('mailpoet_conflict_resolver_whitelist_script', $_this->permittedAssetsLocations['scripts']);
    // unload all scripts except from the list of allowed
    $dequeueScripts = function() use($_this) {
      global $wp_scripts; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      foreach ($wp_scripts->queue as $wpScript) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        if (empty($wp_scripts->registered[$wpScript])) continue; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $registeredScript = $wp_scripts->registered[$wpScript]; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        if (!is_string($registeredScript->src)) {
          continue;
        }
        if (!preg_match('!' . implode('|', $_this->permittedAssetsLocations['scripts']) . '!i', $registeredScript->src)) {
          WPFunctions::get()->wpDequeueScript($wpScript);
        }
      }
    };

    // execute last in the following hooks
    $executeLast = PHP_INT_MAX;
    WPFunctions::get()->addAction('admin_enqueue_scripts', $dequeueScripts, $executeLast);
    WPFunctions::get()->addAction('admin_footer', $dequeueScripts, $executeLast);

    // execute first in hooks for printing (after printing is too late)
    $executeFirst = defined('PHP_INT_MIN') ? constant('PHP_INT_MIN') : ~PHP_INT_MAX;
    WPFunctions::get()->addAction('admin_print_scripts', $dequeueScripts, $executeFirst);
    WPFunctions::get()->addAction('admin_print_footer_scripts', $dequeueScripts, $executeFirst);
  }

  public function resolveEditorConflict() {

    // mark editor as already enqueued to prevent loading its assets
    // when wp_enqueue_editor() used by some other plugin
    global $wp_actions; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $wp_actions['wp_enqueue_editor'] = 1; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    // prevent editor loading when used wp_editor() used by some other plugin
    WPFunctions::get()->addFilter('wp_editor_settings', function () {
      ob_start();
      return [
        'tinymce' => false,
        'quicktags' => false,
      ];
    });

    WPFunctions::get()->addFilter('the_editor', function () {
      return '';
    });

    WPFunctions::get()->addFilter('the_editor_content', function () {
      ob_end_clean();
      return '';
    });
  }

  public function resolveTinyMceConflict() {
    // WordPress TinyMCE scripts may not get enqueued as scripts when some plugins use wp_editor()
    // or wp_enqueue_editor(). Instead, they are printed inside the footer script print actions.
    // To unload TinyMCE we need to remove those actions.
    $tinyMceFooterScriptHooks = [
      '_WP_Editors::enqueue_scripts',
      '_WP_Editors::editor_js',
      '_WP_Editors::force_uncompressed_tinymce',
      '_WP_Editors::print_default_editor_scripts',
    ];

    $disableWpTinymce = function() use ($tinyMceFooterScriptHooks) {
      global $wp_filter; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      $actionName = 'admin_print_footer_scripts';
      if (!isset($wp_filter[$actionName])) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        return;
      }
      foreach ($wp_filter[$actionName]->callbacks as $priority => $callbacks) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        foreach ($tinyMceFooterScriptHooks as $hook) {
          if (isset($callbacks[$hook])) {
            WPFunctions::get()->removeAction($actionName, $callbacks[$hook]['function'], $priority);
          }
        }
      }
    };

    WPFunctions::get()->addAction('admin_footer', $disableWpTinymce, PHP_INT_MAX);
  }
}
