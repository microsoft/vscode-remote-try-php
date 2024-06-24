<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\PageRenderer;
use MailPoet\Logging\LogRepository;
use MailPoetVendor\Carbon\Carbon;

class Logs {
  /** @var PageRenderer */
  private $pageRenderer;

  /** @var LogRepository */
  private $logRepository;

  public function __construct(
    LogRepository $logRepository,
    PageRenderer $pageRenderer
  ) {
    $this->pageRenderer = $pageRenderer;
    $this->logRepository = $logRepository;
  }

  public function render() {
    $search = isset($_GET['search']) ? sanitize_text_field(wp_unslash($_GET['search'])) : null;
    $from = isset($_GET['from']) ? sanitize_text_field(wp_unslash($_GET['from'])) : null;
    $to = isset($_GET['to']) ? sanitize_text_field(wp_unslash($_GET['to'])) : null;
    $offset = isset($_GET['offset']) ? sanitize_text_field(wp_unslash($_GET['offset'])) : null;
    $limit = isset($_GET['limit']) ? sanitize_text_field(wp_unslash($_GET['limit'])) : null;
    $dateFrom = (new Carbon())->subDays(7);
    if (isset($from)) {
      $dateFrom = new Carbon($from);
    }
    $dateTo = null;
    if (isset($to)) {
      $dateTo = new Carbon($to);
    }
    $logs = $this->logRepository->getLogs($dateFrom, $dateTo, $search, $offset, $limit);
    $data = [
      'logs' => [],
    ];
    foreach ($logs as $log) {
      $data['logs'][] = [
        'id' => $log->getId(),
        'name' => $log->getName(),
        'message' => $log->getMessage(),
        'created_at' => ($createdAt = $log->getCreatedAt()) ? $createdAt->format('Y-m-d H:i:s') : null,
      ];
    }
    $this->pageRenderer->displayPage('logs.html', $data);
  }
}
