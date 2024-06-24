<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\Validator;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Annotations\AnnotationReaderProvider;
use MailPoet\Doctrine\PSRMetadataCache;
use MailPoetVendor\Symfony\Component\Validator\Validation;

class ValidatorFactory {
  const METADATA_DIR = __DIR__ . '/../../../generated/validator-metadata';

  /** @var AnnotationReaderProvider */
  private $annotationReaderProvider;

  public function __construct(
    AnnotationReaderProvider $annotationReaderProvider
  ) {
    $this->annotationReaderProvider = $annotationReaderProvider;
  }

  public function createValidator() {
    $builder = Validation::createValidatorBuilder();
    // we need to use our own translator here.
    // If we let the default translator to be used in the builder it uses an anonymous class and that is a problem
    // All integration tests would fail with: [Exception] Serialization of 'class@anonymous' is not allowed
    $translator = new Translator();
    $translator->setLocale('en');
    $builder->setTranslator($translator);

    // annotation reader exists only in dev environment, on production cache is pre-generated
    $annotationReader = $this->annotationReaderProvider->getAnnotationReader();
    if ($annotationReader) {
      $builder->setDoctrineAnnotationReader($annotationReader)
        ->enableAnnotationMapping(true);
    }

    // metadata cache (for production cache is pre-generated at build time)
    $isReadOnly = !$annotationReader;
    $builder->setMappingCache(new PSRMetadataCache(self::METADATA_DIR, $isReadOnly));

    return $builder->getValidator();
  }
}
