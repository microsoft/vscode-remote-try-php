<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\ImportExport\Import;

if (!defined('ABSPATH')) exit;


class MailChimpDataMapper {
  public function getMembersHeader(): array {
    return [
      'email_address',
      'status',
      'first_name',
      'last_name',
      'address',
      'phone',
      'birthday',
      'ip_signup',
      'timestamp_signup',
      'ip_opt',
      'timestamp_opt',
      'member_rating',
      'last_changed',
      'language',
      'vip',
      'email_client',
      'latitude',
      'longitude',
      'gmtoff',
      'dstoff',
      'country_code',
      'timezone',
      'source',
    ];
  }

  public function mapMember(array $member): array {
    return [
      $member['email_address'],
      $member['status'],
      $member['merge_fields']['FNAME'] ?? '',
      $member['merge_fields']['LNAME'] ?? '',
      is_array($member['merge_fields']['ADDRESS']) ? implode(' ', $member['merge_fields']['ADDRESS'] ?? []) : $member['merge_fields']['ADDRESS'],
      $member['merge_fields']['PHONE'] ?? '',
      $member['merge_fields']['BIRTHDAY'] ?? '',
      $member['ip_signup'],
      $member['timestamp_signup'],
      $member['ip_opt'],
      $member['timestamp_opt'],
      $member['member_rating'],
      $member['last_changed'],
      $member['language'],
      $member['vip'],
      $member['email_client'],
      $member['location']['latitude'] ?? '',
      $member['location']['longitude'] ?? '',
      $member['location']['gmtoff'] ?? '',
      $member['location']['dstoff'] ?? '',
      $member['location']['country_code'] ?? '',
      $member['location']['timezone'] ?? '',
      $member['source'],
    ];
  }
}
