<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


class Source {
  const FORM = 'form';
  const IMPORTED = 'imported';
  const ADMINISTRATOR = 'administrator';
  const API = 'api';
  const WORDPRESS_USER = 'wordpress_user';
  const WOOCOMMERCE_USER = 'woocommerce_user';
  const WOOCOMMERCE_CHECKOUT = 'woocommerce_checkout';
  const UNKNOWN = 'unknown';
}
