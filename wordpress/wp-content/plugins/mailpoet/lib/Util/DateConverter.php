<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Carbon\Carbon;

class DateConverter {
  /**
   * @return string|false
   */
  public function convertDateToDatetime(string $date, string $dateFormat) {
    $datetime = false;

    if ($dateFormat === 'datetime') {
      $datetime = $date;
    } elseif ($dateFormat === 'd/m/Y') {
      $datetime = str_replace('/', '-', $date);
    } else {
      $parsedDate = explode('/', $date);
      $parsedDateFormat = explode('/', $dateFormat);
      $yearPosition = array_search('YYYY', $parsedDateFormat);
      $monthPosition = array_search('MM', $parsedDateFormat);
      $dayPosition = array_search('DD', $parsedDateFormat);
      if (count($parsedDate) === 3) {
        // create date from any combination of month, day and year
        $parsedDate = [
          'year' => $parsedDate[$yearPosition],
          'month' => $parsedDate[$monthPosition],
          'day' => $parsedDate[$dayPosition],
        ];
      } else if (count($parsedDate) === 2) {
        // create date from any combination of month and year
        $parsedDate = [
          'year' => $parsedDate[$yearPosition],
          'month' => $parsedDate[$monthPosition],
          'day' => '01',
        ];
      } else if ($dateFormat === 'MM' && count($parsedDate) === 1) {
        // create date from month
        if ((int)$parsedDate[$monthPosition] === 0) {
          $datetime = '';
          $parsedDate = false;
        } else {
          $parsedDate = [
            'month' => $parsedDate[$monthPosition],
            'day' => '01',
            'year' => date('Y'),
          ];
        }
      } else if ($dateFormat === 'YYYY' && count($parsedDate) === 1) {
        // create date from year
        if ((int)$parsedDate[$yearPosition] === 0) {
          $datetime = '';
          $parsedDate = false;
        } else {
          $parsedDate = [
            'year' => $parsedDate[$yearPosition],
            'month' => '01',
            'day' => '01',
          ];
        }
      } else if ($dateFormat === 'DD' && count($parsedDate) === 1) {
        // create date from day
        if ((int)$parsedDate[$dayPosition] === 0) {
          $datetime = '';
          $parsedDate = false;
        } else {
          $parsedDate = [
            'year' => date('Y'),
            'month' => '01',
            'day' => $parsedDate[$dayPosition],
          ];
        }
      } else {
        $parsedDate = false;
      }
      if ($parsedDate) {
        $year = $parsedDate['year'];
        $month = $parsedDate['month'];
        $day = $parsedDate['day'];
        // if all date parts are set to 0, date value is empty
        if ((int)$year === 0 && (int)$month === 0 && (int)$day === 0) {
          $datetime = '';
        } else {
          if ((int)$year === 0) $year = date('Y');
          if ((int)$month === 0) $month = date('m');
          if ((int)$day === 0) $day = date('d');
          $datetime = sprintf(
            '%s-%s-%s 00:00:00',
            $year,
            $month,
            $day
          );
        }
      }
    }
    if ($datetime !== false && !empty($datetime)) {
      try {
        $datetime = Carbon::parse($datetime)->toDateTimeString();
      } catch (\Exception $e) {
        $datetime = false;
      }
    }
    return $datetime;
  }
}
