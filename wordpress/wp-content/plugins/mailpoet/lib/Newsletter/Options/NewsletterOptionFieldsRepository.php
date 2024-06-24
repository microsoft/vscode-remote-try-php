<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Options;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\NewsletterOptionFieldEntity;

/**
 * @extends Repository<NewsletterOptionFieldEntity>
 */
class NewsletterOptionFieldsRepository extends Repository {
  protected function getEntityClassName() {
    return NewsletterOptionFieldEntity::class;
  }
}
