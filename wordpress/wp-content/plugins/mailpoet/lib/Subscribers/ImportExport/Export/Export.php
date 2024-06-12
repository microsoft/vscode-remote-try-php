<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\ImportExport\Export;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Subscribers\ImportExport\ImportExportFactory;
use MailPoet\Subscribers\ImportExport\ImportExportRepository;
use MailPoet\Util\Security;
use MailPoetVendor\XLSXWriter;

class Export {
  const SUBSCRIBER_BATCH_SIZE = 15000;

  public $exportFormatOption;
  public $subscriberFields;
  public $subscriberCustomFields;
  public $formattedSubscriberFields;
  public $formattedSubscriberFieldsWithList;
  public $exportPath;
  public $exportFile;
  public $exportFileURL;

  /** @var int */
  private $subscribersOffset;

  /** @var array<SegmentEntity|null> null value is for subscribers without a list */
  private $segments;

  /** @var int */
  private $segmentIndex;

  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  /** @var ImportExportRepository */
  private $importExportRepository;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  public function __construct(
    CustomFieldsRepository $customFieldsRepository,
    ImportExportRepository $importExportRepository,
    SegmentsRepository $segmentsRepository,
    array $data
  ) {
    $this->customFieldsRepository = $customFieldsRepository;
    $this->importExportRepository = $importExportRepository;
    $this->segmentsRepository = $segmentsRepository;
    if (strpos((string)@ini_get('disable_functions'), 'set_time_limit') === false) {
      set_time_limit(0);
    }

    $this->subscribersOffset = 0;
    $this->segmentIndex = 0;
    $this->segments = $this->getSegments($data['segments']);
    $this->exportFormatOption = $data['export_format_option'];
    $this->subscriberFields = $data['subscriber_fields'];
    $this->subscriberCustomFields = $this->getSubscriberCustomFields();
    $this->formattedSubscriberFields = $this->formatSubscriberFields(
      $this->subscriberFields,
      $this->subscriberCustomFields
    );
    $this->formattedSubscriberFieldsWithList = $this->formattedSubscriberFields;
    $this->formattedSubscriberFieldsWithList[] = __('List', 'mailpoet');
    $this->exportPath = self::getExportPath();
    $this->exportFile = $this->getExportFile($this->exportFormatOption);
    $this->exportFileURL = $this->getExportFileURL($this->exportFile);
  }

  public static function getFilePrefix() {
    return 'MailPoet_export_';
  }

  public static function getExportPath() {
    return Env::$tempPath;
  }

  public function process(): array {
    $processedSubscribers = 0;
    $this->resetCounters();
    try {
      if (is_writable($this->exportPath) === false) {
        throw new \Exception(__('The export file could not be saved on the server.', 'mailpoet'));
      }
      if (!extension_loaded('zip') && ($this->exportFormatOption === 'xlsx')) {
        throw new \Exception(__('Export requires a ZIP extension to be installed on the host.', 'mailpoet'));
      }
      $callback = [
        $this,
        'generate' . strtoupper($this->exportFormatOption),
      ];
      if (is_callable($callback)) {
        $processedSubscribers = call_user_func($callback);
      }
    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }
    return [
      'totalExported' => $processedSubscribers,
      'exportFileURL' => $this->exportFileURL,
    ];
  }

  public function generateCSV(): int {
    $processedSubscribers = 0;
    $formattedSubscriberFields = $this->formattedSubscriberFieldsWithList;
    $cSVFile = fopen($this->exportFile, 'w');
    if ($cSVFile === false) {
      throw new \Exception(__('Failed opening file for export.', 'mailpoet'));
    }
    $formatCSV = function($row) {
      return '"' . str_replace('"', '\"', (string)$row) . '"';
    };
    // add UTF-8 BOM (3 bytes, hex EF BB BF) at the start of the file for
    // Excel to automatically recognize the encoding
    fwrite($cSVFile, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fwrite(
      $cSVFile,
      implode(
        ',',
        array_map(
          $formatCSV,
          $formattedSubscriberFields
        )
      ) . PHP_EOL
    );

    while (($subscribers = $this->getSubscribers()) !== null) {
      $processedSubscribers += count($subscribers);
      foreach ($subscribers as $subscriber) {
        $row = $this->formatSubscriberData($subscriber);
        $row[] = ucwords($subscriber['segment_name']);
        fwrite($cSVFile, implode(',', array_map($formatCSV, $row)) . "\n");
      }
    }
    fclose($cSVFile);
    return $processedSubscribers;
  }

  public function generateXLSX(): int {
    $processedSubscribers = 0;
    $xLSXWriter = new XLSXWriter();
    $xLSXWriter->setAuthor('MailPoet (www.mailpoet.com)');
    $lastSegment = false;
    $processedSegments = [];

    while (($subscribers = $this->getSubscribers()) !== null) {
      $processedSubscribers += count($subscribers);
      foreach ($subscribers as $i => $subscriber) {
        $currentSegment = ucwords($subscriber['segment_name']);
        // Sheet header (1st row) will be written only if:
        // * This is the first time we're processing a segment
        // * The previous subscriber's segment is different from the current subscriber's segment
        // Header will NOT be written if:
        // * We have already processed the segment. Because SQL results are not
        // sorted by segment name (due to slow queries when using ORDER BY and LIMIT),
        // we need to keep track of processed segments so that we do not create header
        // multiple times when switching from one segment to another and back.
        if (
          (!count($processedSegments) || $lastSegment !== $currentSegment) &&
          (!in_array($lastSegment, $processedSegments) || !in_array($currentSegment, $processedSegments))
        ) {
          $this->writeXLSX(
            $xLSXWriter,
            $subscriber['segment_name'],
            $this->formattedSubscriberFieldsWithList
          );
          $processedSegments[] = $currentSegment;
        }
        $lastSegment = ucwords($subscriber['segment_name']);
        // detect RTL language and set Excel to properly display the sheet
        $rTLRegex = '/\p{Arabic}|\p{Hebrew}/u';
        if (
          !$xLSXWriter->rtl && (
            preg_grep($rTLRegex, $subscriber) ||
            preg_grep($rTLRegex, $this->formattedSubscriberFieldsWithList))
        ) {
          $xLSXWriter->rtl = true;
        }

        $xlsxData = $this->formatSubscriberData($subscriber);
        $xlsxData[] = ucwords($subscriber['segment_name']);

        $this->writeXLSX(
          $xLSXWriter,
          $lastSegment,
          $xlsxData
        );
      }
    }
    $xLSXWriter->writeToFile($this->exportFile);
    return $processedSubscribers;
  }

  public function writeXLSX($xLSXWriter, $segment, $data) {
    return $xLSXWriter->writeSheetRow(ucwords($segment), $data);
  }

  public function getSubscribers(): ?array {
    $segment = array_key_exists($this->segmentIndex, $this->segments) ? $this->segments[$this->segmentIndex] : false;
    if ($segment === false) {
      return null;
    }

    $subscribers = $this->importExportRepository->getSubscribersBatchBySegment(
      $segment,
      self::SUBSCRIBER_BATCH_SIZE,
      $this->subscribersOffset
    );
    $this->subscribersOffset += count($subscribers);

    if (count($subscribers) < self::SUBSCRIBER_BATCH_SIZE) {
      $this->segmentIndex++;
      $this->subscribersOffset = 0;
    }

    return $subscribers;
  }

  public function getExportFileURL($file): string {
    return sprintf(
      '%s/%s',
      Env::$tempUrl,
      basename($file)
    );
  }

  public function getExportFile($format): string {
    return sprintf(
      $this->exportPath . '/' . self::getFilePrefix() . '%s.%s',
      Security::generateRandomString(15),
      $format
    );
  }

  /**
   * @return array<int, string>
   */
  public function getSubscriberCustomFields(): array {
    $result = [];
    foreach ($this->customFieldsRepository->findAll() as $customField) {
      $result[(int)$customField->getId()] = $customField->getName();
    }
    return $result;
  }

  /**
   * @param array $segmentIds
   * @return array<SegmentEntity|null> null value is for subscribers without a list
   */
  private function getSegments(array $segmentIds): array {
    $segments = $this->segmentsRepository->findBy(['id' => $segmentIds]);
    $result = [];
    foreach ($segmentIds as $segmentId) {
      $segmentId = (int)$segmentId;
      $segment = current(array_filter($segments, function (SegmentEntity $segment) use ($segmentId): bool {
        return $segment->getId() === $segmentId;
      })) ?: null;

      if (!$segment && $segmentId !== 0) {
        continue;
      }

      $result[] = $segment;
    }

    return $result;
  }

  private function resetCounters(): void {
    $this->segmentIndex = 0;
    $this->subscribersOffset = 0;
  }

  public function formatSubscriberFields($subscriberFields, $subscriberCustomFields): array {
    $exportFactory = new ImportExportFactory('export');
    $translatedFields = $exportFactory->getSubscriberFields();
    return array_map(function($field) use (
      $translatedFields, $subscriberCustomFields
    ) {
      $field = (isset($translatedFields[$field])) ?
        ucfirst($translatedFields[$field]) :
        ucfirst($field);
      return (isset($subscriberCustomFields[$field])) ?
        ucfirst($subscriberCustomFields[$field]) : $field;
    }, $subscriberFields);
  }

  public function formatSubscriberData($subscriber): array {
    return array_map(function($field) use ($subscriber) {
      return $subscriber[$field];
    }, $this->subscriberFields);
  }
}
