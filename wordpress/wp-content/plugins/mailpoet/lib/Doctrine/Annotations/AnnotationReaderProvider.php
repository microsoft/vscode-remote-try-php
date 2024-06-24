<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\Annotations;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\PSRArrayCache;
use MailPoetVendor\Doctrine\Common\Annotations\AnnotationReader;
use MailPoetVendor\Doctrine\Common\Annotations\AnnotationRegistry;
use MailPoetVendor\Doctrine\Common\Annotations\PsrCachedReader;

class AnnotationReaderProvider {
  /** @var PsrCachedReader */
  private $annotationReader;

  public function __construct() {
    // register annotation reader if doctrine/annotations package is installed
    // (i.e. in dev environment, on production metadata is dumped in the build)
    $readAnnotations = class_exists(PsrCachedReader::class) && class_exists(AnnotationReader::class);
    if ($readAnnotations) {
      // autoload all annotation classes using registered loaders (Composer)
      // (needed for Symfony\Validator constraint annotations to be loaded)
      AnnotationRegistry::registerLoader('class_exists');
      $this->annotationReader = new PsrCachedReader(new AnnotationReader(), new PSRArrayCache());
    }
  }

  public function getAnnotationReader(): ?PsrCachedReader {
    return $this->annotationReader;
  }
}
