<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\NewsletterTemplates;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoet\Entities\NewsletterTemplateEntity;
use MailPoet\Util\Security;
use MailPoet\WP\Functions as WPFunctions;

class ThumbnailSaver {
  const THUMBNAIL_DIRECTORY = 'newsletter_thumbnails';
  const IMAGE_QUALITY = 80;

  /** @var NewsletterTemplatesRepository */
  private $repository;

  /** @var WPFunctions */
  private $wp;

  /** @var string */
  private $baseDirectory;

  /** @var string */
  private $baseUrl;

  public function __construct(
    NewsletterTemplatesRepository $repository,
    WPFunctions $wp
  ) {
    $this->repository = $repository;
    $this->wp = $wp;
    $this->baseDirectory = Env::$tempPath;
    $this->baseUrl = Env::$tempUrl;
  }

  public function ensureTemplateThumbnailsForAll() {
    $templateIds = $this->repository->getIdsOfEditableTemplates();
    foreach ($templateIds as $templateId) {
      $template = $this->repository->findOneById((int)$templateId);
      if (!$template) continue;
      $this->ensureTemplateThumbnailFile($template);
      // Remove template entity from memory after it was processed
      $this->repository->detach($template);
      unset($template);
    }
  }

  public function ensureTemplateThumbnailFile(NewsletterTemplateEntity $template): NewsletterTemplateEntity {
    if ($template->getReadonly()) {
      return $template;
    }
    $thumbnailUrl = $template->getThumbnail();
    $savedFilename = null;
    $savedBaseUrl = null;
    if ($thumbnailUrl && strpos($thumbnailUrl, self::THUMBNAIL_DIRECTORY) !== false) {
      [$savedBaseUrl, $savedFilename] = explode('/' . self::THUMBNAIL_DIRECTORY . '/', $thumbnailUrl ?? '');
    }
    $file = $this->baseDirectory . '/' . self::THUMBNAIL_DIRECTORY . '/' . $savedFilename;
    if (!$savedFilename || !file_exists($file)) {
      $this->saveTemplateImage($template);
    }

    // File might exist but domain was changed
    $thumbnailUrl = $template->getThumbnail();
    if ($savedBaseUrl && $savedBaseUrl !== $this->baseUrl && $thumbnailUrl) {
      $template->setThumbnail(str_replace($savedBaseUrl, $this->baseUrl, $thumbnailUrl));
    }
    return $template;
  }

  private function saveTemplateImage(NewsletterTemplateEntity $template): void {
    $data = $template->getThumbnailData();
    if (!$data) {
      return;
    }
    // Check that data contains Base 64 encoded jpeg
    if (strpos($data, 'data:image/jpeg;base64') !== 0) {
      return;
    }
    $thumbNailsDirectory = $this->baseDirectory . '/' . self::THUMBNAIL_DIRECTORY;
    if (!file_exists($thumbNailsDirectory)) {
      $this->wp->wpMkdirP($thumbNailsDirectory);
    }
    $file = $thumbNailsDirectory . '/' . Security::generateHash(16) . '_template_' . $template->getId() . '.jpg';

    // Save the original quality image to a file and update DB record
    if (!$this->saveBase64AsImageFile($file, $data)) {
      return;
    }
    $url = str_replace($this->baseDirectory, $this->baseUrl, $file);
    $template->setThumbnail($url);
    $this->repository->flush();

    // It is important that compression happens after the url was saved to DB.
    // For some large files there is a risk that compression (if done using GD library) may fail due hitting memory limit.
    // This way if the error occures the url is already saved and next time (e.g. next cron run) the image will be skipped
    // and the previously saved original quality image used
    $this->compressImage($file);
  }

  private function compressImage(string $file): bool {
    $editor = $this->wp->wpGetImageEditor($file);
    if ($editor instanceof \WP_Error) {
      return false;
    }
    $result = $editor->set_quality(self::IMAGE_QUALITY);
    if ($result instanceof \WP_Error) {
      return false;
    }
    $result = $editor->save($file);
    if ($result instanceof \WP_Error) {
      return false;
    }
    unset($editor);
    return true;
  }

  /**
   * Simply saves base64 to a file without any compression
   * @return bool
   */
  private function saveBase64AsImageFile(string $file, string $data): bool {
    return file_put_contents($file, file_get_contents($data)) !== false;
  }
}
