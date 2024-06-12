<?php declare(strict_types = 1);

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\CarbonImmutable;
use Tracy\Debugger;
use Tracy\ILogger;
use WP_Error;

class TranslationUpdater {
  const API_UPDATES_BASE_URI = 'https://translate.wordpress.com/api/translations-updates/mailpoet/';
  const MAILPOET_FREE_DOT_COM_PROJECT_ID = 'MailPoet - MailPoet';
  const TRANSIENT_KEY_PREFIX = 'mailpoet_translation_updates_';
  const TRANSIENT_EXPIRATION = 300; // 5 minutes

  /** @var WPFunctions */
  private $wpFunctions;

  /** @var string */
  private $freeSlug;

  /** @var string */
  private $freeVersion;

  /** @var string */
  private $premiumSlug;

  /** @var string|null */
  private $premiumVersion;

  public function __construct(
    WPFunctions $wpFunctions,
    string $freeSlug,
    string $freeVersion,
    string $premiumSlug,
    ?string $premiumVersion
  ) {
    $this->wpFunctions = $wpFunctions;
    $this->freeSlug = $freeSlug;
    $this->freeVersion = $freeVersion;
    $this->premiumSlug = $premiumSlug;
    $this->premiumVersion = $premiumVersion;
  }

  public function init(): void {
    $this->wpFunctions->addFilter('pre_set_site_transient_update_plugins', [$this, 'checkForTranslations'], 21);
  }

  public function checkForTranslations($transient) {
    if (!$transient instanceof \stdClass) {
      $transient = new \stdClass;
    }

    $locales = $this->getLocales();
    if (empty($locales)) {
      return $transient;
    }

    $languagePacksData = $this->getAvailableTranslations($locales);
    $translations = $this->selectUpdatesToInstall($languagePacksData);
    // We want to ignore translations from .org in case a translation pack for the same locale is available from .com
    $dotOrgTranslations = $this->removeDuplicateTranslationsFromOrg($transient->translations ?? [], $languagePacksData[$this->freeSlug] ?? []);
    $transient->translations = array_merge($dotOrgTranslations ?? [], $translations);
    return $transient;
  }

  /**
   * Find available languages
   * @return array
   */
  private function getLocales(): array {
    $locales = array_values($this->wpFunctions->getAvailableLanguages());
    $locales = apply_filters('plugins_update_check_locales', $locales);
    return array_unique($locales);
  }

  private function getAvailableTranslations(array $locales): array {
    $requestBody = [
      'locales' => $locales,
      'plugins' => [
        $this->freeSlug => ['version' => $this->freeVersion],
      ],
    ];
    if ($this->premiumVersion) {
      $requestBody['plugins'][$this->premiumSlug] = ['version' => $this->premiumVersion];
    }

    $cacheKey = self::TRANSIENT_KEY_PREFIX . md5(serialize($requestBody));
    $rawResponse = $this->wpFunctions->getTransient($cacheKey);
    if (!$rawResponse) {
      $rawResponse = $this->fetchApiResponse($requestBody);
      if ($rawResponse instanceof WP_Error) {
        // Don't continue if there was an error.
        $this->logError("MailPoet: Failed to fetch translations from WordPress.com API with error: " . $rawResponse->get_error_message());
        return [];
      }

      $responseCode = $this->wpFunctions->wpRemoteRetrieveResponseCode($rawResponse);
      // Wait a couple of seconds and retry when 429 is returned.
      if ($responseCode === 429) {
        sleep(2);
        $rawResponse = $this->fetchApiResponse($requestBody);
        if ($rawResponse instanceof WP_Error) {
          // Don't continue if there was an error.
          $this->logError("MailPoet: Failed retrying to fetch translations from WordPress.com API with error: " . $rawResponse->get_error_message());
          return [];
        }
        $responseCode = $this->wpFunctions->wpRemoteRetrieveResponseCode($rawResponse);
      }
      // Don't continue when API request failed.
      if ($responseCode !== 200) {
        $this->logError("MailPoet: Failed to fetch translations from WordPress.com API with $responseCode and response message: " . $this->wpFunctions->wpRemoteRetrieveResponseMessage($rawResponse));
        return [];
      }
      $this->wpFunctions->setTransient($cacheKey, $rawResponse, self::TRANSIENT_EXPIRATION);
    }
    $response = json_decode($this->wpFunctions->wpRemoteRetrieveBody($rawResponse), true);
    if (!is_array($response) || (array_key_exists('success', $response) && $response['success'] === false)) {
      $this->logError("MailPoet: Failed to fetch translations from WordPress.com API with code 200 and response: " . json_encode($response));
      return [];
    }
    return $response['data'];
  }

  private function selectUpdatesToInstall(array $responseData) {
    $installedTranslations = $this->wpFunctions->wpGetInstalledTranslations('plugins');
    $translationsToInstall = [];
    foreach ($responseData as $pluginName => $languagePacks) {
      foreach ($languagePacks as $languagePack) {
        // Check revision date if translation is already installed.
        if (array_key_exists($pluginName, $installedTranslations) && array_key_exists($languagePack['wp_locale'], $installedTranslations[$pluginName])) {
          $installedFromWpOrg = ($pluginName === $this->freeSlug) && ($installedTranslations[$pluginName][$languagePack['wp_locale']]['Project-Id-Version'] !== self::MAILPOET_FREE_DOT_COM_PROJECT_ID);
          $installedTranslationRevisionTime = new CarbonImmutable($installedTranslations[$pluginName][$languagePack['wp_locale']]['PO-Revision-Date']);
          $newTranslationRevisionTime = new CarbonImmutable($languagePack['last_modified']);

          // In case installed translation pack comes from WP.org make sure that the one coming from WP.com has newer date
          if ($installedFromWpOrg && $newTranslationRevisionTime <= $installedTranslationRevisionTime) {
            $languagePack['last_modified'] = $installedTranslationRevisionTime->addSecond()->toDateTimeString();
            $newTranslationRevisionTime = new CarbonImmutable($languagePack['last_modified']);
          }

          // Skip if translation language pack is not newer than what is installed already.
          if ($newTranslationRevisionTime <= $installedTranslationRevisionTime) {
            continue;
          }
        }
        $translationsToInstall[] = [
          'type' => 'plugin',
          'slug' => $pluginName,
          'language' => $languagePack['wp_locale'],
          'version' => $languagePack['version'],
          'updated' => $languagePack['last_modified'],
          'package' => $languagePack['package'],
          'autoupdate' => true,
        ];
      }
    }

    return $translationsToInstall;
  }

  private function removeDuplicateTranslationsFromOrg(array $translationsDotOrg, array $translationsDotComData) {
    $localesAvailableFromDotCom = array_unique(array_column($translationsDotComData, 'wp_locale'));
    return array_filter($translationsDotOrg, function ($translation) use($localesAvailableFromDotCom) {
      if (
        $translation['slug'] !== $this->freeSlug
        || !in_array($translation['language'], $localesAvailableFromDotCom, true)
      ) {
        return true;
      }
      return false;
    });
  }

  private function logError(string $message): void {
    if (class_exists(Debugger::class)) {
      Debugger::log($message, ILogger::ERROR);
    }
    if (function_exists('error_log')) {
      error_log($message); // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
    }
  }

  /**
   * @return array|\WP_Error
   */
  private function fetchApiResponse(array $requestBody) {
    // Ten seconds, plus one extra second for every 10 locales.
    $timeout = 10 + (int)(count($requestBody['locales']) / 10);

    $body = wp_json_encode($requestBody);
    if ($body === false) {
      return new WP_Error('wp_json_encode_error', 'Failed to encode request body to retrieve translations');
    }

    $response = $this->wpFunctions->wpRemotePost(self::API_UPDATES_BASE_URI, [
      'body' => $body,
      'headers' => ['Content-Type: application/json'],
      'timeout' => $timeout,
    ]);
    return $response;
  }
}
