<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\PageRenderer;
use MailPoet\Subscribers\ImportExport\ImportExportFactory;

class SubscribersExport {
  /** @var PageRenderer */
  private $pageRenderer;

  public function __construct(
    PageRenderer $pageRenderer
  ) {
    $this->pageRenderer = $pageRenderer;
  }

  public function render() {
    $export = new ImportExportFactory(ImportExportFactory::EXPORT_ACTION);
    $data = $export->bootstrap();
    $this->pageRenderer->displayPage('subscribers/importExport/export.html', $data);
  }
}
