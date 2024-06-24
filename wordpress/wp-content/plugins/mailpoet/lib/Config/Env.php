<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class Env {
  const NEWSLETTER_CONTENT_WIDTH = 1320;

  public static $version;
  public static $pluginName;
  public static $pluginPath;
  public static $baseUrl;
  public static $file;
  public static $path;
  public static $viewsPath;
  public static $assetsPath;
  public static $assetsUrl;
  public static $utilPath;
  public static $tempPath;
  public static $cachePath;
  public static $tempUrl;
  public static $languagesPath;
  public static $libPath;
  public static $pluginPrefix;
  /** @var string WP DB prefix + plugin prefix */
  public static $dbPrefix;
  /** @var string WP DB prefix only */
  public static $wpDbPrefix;
  public static $dbHost;
  public static $dbIsIpv6;
  public static $dbSocket;
  public static $dbPort;
  public static $dbName;
  public static $dbUsername;
  public static $dbPassword;
  public static $dbCharset;
  public static $dbCollation;
  public static $dbCharsetCollate;
  public static $dbTimezoneOffset;

  // back compatibility for older Premium plugin with underscore naming
  // (we need to allow it to activate so it can render an update notice)
  public static $plugin_name; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
  public static $temp_path; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

  public static function init($file, $version, $dbHost, $dbUser, $dbPassword, $dbName) {
    self::$version = $version;
    self::$file = $file;
    self::$path = dirname(self::$file);
    self::$pluginName = 'mailpoet';
    self::$pluginPath = 'mailpoet/mailpoet.php';
    self::$baseUrl = WPFunctions::get()->pluginsUrl('', $file);
    self::$viewsPath = self::$path . '/views';
    self::$assetsPath = self::$path . '/assets';
    self::$assetsUrl = WPFunctions::get()->pluginsUrl('/assets', $file);
    self::$utilPath = self::$path . '/lib/Util';
    $wpUploadDir = WPFunctions::get()->wpUploadDir();
    self::$tempPath = $wpUploadDir['basedir'] . '/' . self::$pluginName;
    self::$cachePath = self::$path . '/generated/twig/';
    self::$tempUrl = $wpUploadDir['baseurl'] . '/' . self::$pluginName;
    self::$languagesPath = self::$path . '/../../languages/plugins/';
    self::$libPath = self::$path . '/lib';
    self::$pluginPrefix = WPFunctions::get()->applyFilters('mailpoet_db_prefix', 'mailpoet_');
    self::initDbParameters($dbHost, $dbUser, $dbPassword, $dbName);

    // back compatibility for older Premium plugin with underscore naming
    self::$plugin_name = self::$pluginName; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    self::$temp_path = self::$tempPath; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
  }

  /**
   * @see https://codex.wordpress.org/Editing_wp-config.php#Set_Database_Host for possible DB_HOSTS values
   */
  private static function initDbParameters($dbHost, $dbUser, $dbPassword, $dbName) {
    $parsedHost = WPFunctions::get()->parseDbHost($dbHost);
    if ($parsedHost === false) {
      throw new \InvalidArgumentException('Invalid db host configuration.');
    }
    [$host, $port, $socket, $isIpv6] = $parsedHost;

    global $wpdb;
    self::$dbPrefix = $wpdb->prefix . self::$pluginPrefix;
    self::$wpDbPrefix = $wpdb->prefix;
    self::$dbHost = $host;
    self::$dbIsIpv6 = $isIpv6;
    self::$dbPort = $port;
    self::$dbSocket = $socket;
    self::$dbName = $dbName;
    self::$dbUsername = $dbUser;
    self::$dbPassword = $dbPassword;
    self::$dbCharset = $wpdb->charset;
    self::$dbCollation = $wpdb->collate;
    self::$dbCharsetCollate = $wpdb->get_charset_collate();
    self::$dbTimezoneOffset = self::getDbTimezoneOffset();
  }

  public static function getDbTimezoneOffset($offset = false) {
    $offset = ($offset) ? $offset : WPFunctions::get()->getOption('gmt_offset');
    $offset = (float)($offset);
    $mins = $offset * 60;
    $sgn = ($mins < 0 ? -1 : 1);
    $mins = abs($mins);
    $hrs = floor($mins / 60);
    $mins -= $hrs * 60;
    return sprintf('%+03d:%02d', $hrs * $sgn, $mins);
  }
}
