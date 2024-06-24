<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\Persistence\Mapping\ProxyClassNameResolver as IProxyClassNameResolver;

/**
 * This is exact copy of an anonymous class from \MailPoetVendor\Doctrine\Persistence\Mapping\AbstractClassMetadataFactory
 * We need to use a non-anonymous class so that it is serializable within integration tests
 * @see https://github.com/doctrine/persistence/blob/2.2.x/lib/Doctrine/Persistence/Mapping/AbstractClassMetadataFactory.php#L516-L536
 */
class ProxyClassNameResolver implements IProxyClassNameResolver {
  /**
   * @template T
   * @return class-string<T>
   */
  public function resolveClassName(string $className): string {
    $pos = \strrpos($className, '\\' . \MailPoetVendor\Doctrine\Persistence\Proxy::MARKER . '\\');
    if ($pos === \false) {
      /** @var class-string<T> */
      return $className;
    }
    /** @var class-string<T> */
    return \substr($className, $pos + \MailPoetVendor\Doctrine\Persistence\Proxy::MARKER_LENGTH + 2);
  }
}
