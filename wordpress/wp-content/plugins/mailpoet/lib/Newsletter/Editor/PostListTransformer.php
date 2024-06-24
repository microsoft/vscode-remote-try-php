<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Editor;

if (!defined('ABSPATH')) exit;


class PostListTransformer {

  private $args;
  private $transformer;

  public function __construct(
    $args
  ) {
    $this->args = $args;
    $this->transformer = new PostTransformer($args);
  }

  public function transform($posts) {
    $results = [];
    $useDivider = filter_var($this->args['showDivider'], FILTER_VALIDATE_BOOLEAN);

    foreach ($posts as $index => $post) {
      if ($useDivider && $index > 0) {
        $results[] = $this->transformer->getDivider();
      }

      $results = array_merge($results, $this->transformer->transform($post));
    }

    return $results;
  }
}
