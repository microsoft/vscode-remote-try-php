<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class API {
  /**
   * @param string $version
   * @return \MailPoet\API\MP\v1\API
   * @throws \Exception
   */
  public static function MP($version) {
    /** @var class-string<\MailPoet\API\MP\v1\API> $apiClass */
    $apiClass = sprintf('%s\MP\%s\API', __NAMESPACE__, $version);
    try {
      return ContainerWrapper::getInstance()->get($apiClass);
    } catch (ServiceNotFoundException $e) {
      throw new \Exception(__('Invalid API version.', 'mailpoet'));
    }
  }
}
