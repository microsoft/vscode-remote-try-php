<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine;

if (!defined('ABSPATH')) exit;


use MailPoet\Validator\Builder;

class EmailStylesSchema {
  public function getSchema(): array {
    $typographyProps = Builder::object([
      'fontFamily' => Builder::string()->nullable(),
      'fontSize' => Builder::string()->nullable(),
      'fontStyle' => Builder::string()->nullable(),
      'fontWeight' => Builder::string()->nullable(),
      'letterSpacing' => Builder::string()->nullable(),
      'lineHeight' => Builder::string()->nullable(),
      'textTransform' => Builder::string()->nullable(),
      'textDecoration' => Builder::string()->nullable(),
    ])->nullable();
    return Builder::object([
      'version' => Builder::integer(),
      'styles' => Builder::object([
        'spacing' => Builder::object([
          'padding' => Builder::object([
            'top' => Builder::string(),
            'right' => Builder::string(),
            'bottom' => Builder::string(),
            'left' => Builder::string(),
          ])->nullable(),
          'blockGap' => Builder::string()->nullable(),
        ])->nullable(),
        'color' => Builder::object([
          'background' => Builder::string()->nullable(),
          'text' => Builder::string()->nullable(),
        ])->nullable(),
        'typography' => $typographyProps,
        'elements' => Builder::object([
          'heading' => Builder::object([
            'typography' => $typographyProps,
          ])->nullable(),
          'button' => Builder::object([
            'typography' => $typographyProps,
          ])->nullable(),
          'link' => Builder::object([
            'typography' => $typographyProps,
          ])->nullable(),
          'h1' => Builder::object([
            'typography' => $typographyProps,
          ])->nullable(),
          'h2' => Builder::object([
            'typography' => $typographyProps,
          ])->nullable(),
          'h3' => Builder::object([
            'typography' => $typographyProps,
          ])->nullable(),
          'h4' => Builder::object([
            'typography' => $typographyProps,
          ])->nullable(),
          'h5' => Builder::object([
            'typography' => $typographyProps,
          ])->nullable(),
          'h6' => Builder::object([
            'typography' => $typographyProps,
          ])->nullable(),
        ])->nullable(),
      ])->nullable(),
    ])->toArray();
  }
}
