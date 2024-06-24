<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Templates\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\Templates\FormTemplate;

class InitialForm extends FormTemplate {
  const ID = 'initial_form';

  public function getName(): string {
    return '';
  }

  public function getThumbnailUrl(): string {
    return '';
  }

  public function getBody(): array {
    return [
      [
        'id' => 'email',
        'name' => __('Email', 'mailpoet'),
        'type' => 'text',
        'params' => [
          'label' => __('Email Address', 'mailpoet'),
          'required' => true,
          'label_within' => true,
        ],
        'styles' => [
          'full_width' => true,
        ],
      ],
      [
        'id' => 'submit',
        'name' => __('Submit', 'mailpoet'),
        'type' => 'submit',
        'params' => [
          'label' => __('Subscribe!', 'mailpoet'),
        ],
        'styles' => [
          'full_width' => true,
        ],
      ],
    ];
  }
}
