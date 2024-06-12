<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Templates\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\Templates\FormTemplate;

class Template3FixedBar extends FormTemplate {
  const ID = 'template_3_fixed_bar';

  /** @var string */
  protected $assetsDirectory = 'template-3';

  public function getName(): string {
    return _x('Welcome Discount', 'Form template name', 'mailpoet');
  }

  public function getThumbnailUrl(): string {
    return $this->getAssetUrl('fixedbar.png');
  }

  public function getBody(): array {
    return [
      [
        'type' => 'columns',
        'body' => [
          [
            'type' => 'column',
            'params' => [
              'class_name' => '',
              'vertical_alignment' => 'center',
              'width' => '67.5',
            ],
            'body' => [
              [
                'type' => 'heading',
                'id' => 'heading',
                'params' => [
                  'content' => '<span style="font-family: Montserrat" data-font="Montserrat" class="mailpoet-has-font"><strong>' . _x('10% off, <br>especially for you', 'Text in a web form. Keep HTML tags!', 'mailpoet') . '</strong></span>' . ' ' . $this->wp->wpStaticizeEmoji('🎁'),
                  'level' => '1',
                  'align' => 'left',
                  'font_size' => '30',
                  'text_color' => '#000000',
                  'line_height' => '1.5',
                  'background_color' => '',
                  'anchor' => 'block-heading_0.27494222669689683-1595510796066',
                  'class_name' => '',
                ],
              ],
              [
                'type' => 'heading',
                'id' => 'heading',
                'params' => [
                  'content' => '<span style="font-family: Montserrat" data-font="Montserrat" class="mailpoet-has-font"><strong>' . _x('Sign up to receive your exclusive discount,<br>and keep up to date on our latest products & offers!', 'Text in a web form. Keep HTML tags!', 'mailpoet') . '</strong></span>',
                  'level' => '2',
                  'align' => 'left',
                  'font_size' => '16',
                  'text_color' => '#000000',
                  'line_height' => '1.7',
                  'background_color' => '',
                  'anchor' => '',
                  'class_name' => '',
                ],
              ],
              [
                'type' => 'paragraph',
                'id' => 'paragraph',
                'params' => [
                  'content' => '<em>' . $this->replacePrivacyLinkTags(_x('We don’t spam! Read our [link]privacy policy[/link] for more info.', 'Text in a web form.', 'mailpoet'), '#') . '</em>',
                  'drop_cap' => '0',
                  'align' => 'left',
                  'font_size' => '14',
                  'line_height' => '1.5',
                  'text_color' => '',
                  'background_color' => '',
                  'class_name' => '',
                ],
              ],
            ],
          ],
          [
            'type' => 'column',
            'params' => [
              'class_name' => '',
              'vertical_alignment' => 'center',
              'width' => '32.5',
            ],
            'body' => [
              [
                'type' => 'text',
                'params' => [
                  'label' => _x('Email Address', 'Form label', 'mailpoet'),
                  'class_name' => '',
                  'required' => '1',
                  'label_within' => '1',
                ],
                'id' => 'email',
                'name' => 'Email',
                'styles' => [
                  'full_width' => '1',
                  'bold' => '0',
                  'background_color' => '#ffffff',
                  'border_size' => '1',
                  'border_radius' => '0',
                  'border_color' => '#313131',
                ],
              ],
              [
                'type' => 'submit',
                'params' => [
                  'label' => _x('Save 10%', 'Form label', 'mailpoet'),
                  'class_name' => '',
                ],
                'id' => 'submit',
                'name' => 'Submit',
                'styles' => [
                  'full_width' => '1',
                  'bold' => '1',
                  'background_color' => '#000000',
                  'font_size' => '16',
                  'font_color' => '#ffffff',
                  'border_size' => '1',
                  'border_radius' => '2',
                  'border_color' => '#313131',
                  'padding' => '12',
                  'font_family' => 'Montserrat',
                ],
              ],
            ],
          ],
        ],
        'params' => [
          'vertical_alignment' => '',
          'class_name' => '',
          'text_color' => '',
          'background_color' => '',
          'gradient' => '',
        ],
      ],
    ];
  }

  public function getSettings(): array {
    return [
      'on_success' => 'message',
      'success_message' => '',
      'segments' => [],
      'segments_selected_by' => 'admin',
      'alignment' => 'left',
      'form_placement' => [
        'popup' => ['enabled' => ''],
        'below_posts' => ['enabled' => ''],
        'fixed_bar' => [
          'enabled' => '1',
          'styles' => [
            'width' => [
              'unit' => 'pixel',
              'value' => '1100',
            ],
          ],
        ],
        'slide_in' => ['enabled' => ''],
        'others' => [],
      ],
      'border_radius' => '2',
      'border_size' => '0',
      'form_padding' => '16',
      'input_padding' => '12',
      'success_validation_color' => '#00d084',
      'error_validation_color' => '#cf2e2e',
      'close_button' => 'classic',
      'fontSize' => '16',
      'font_family' => 'Montserrat',
    ];
  }

  public function getStyles(): string {
    return <<<EOL
/* form */
.mailpoet_form {
}

form {
  margin-bottom: 0;
}

p.mailpoet_form_paragraph {
    margin-bottom: 10px;
}

/* columns */
.mailpoet_column_with_background {
  padding: 10px;
}
/* space between columns */
.mailpoet_form_column:not(:first-child) {
  margin-left: 20px;
}

/* input wrapper (label + input) */
.mailpoet_paragraph {
  line-height:20px;
  margin-bottom: 20px;
}

/* labels */
.mailpoet_segment_label,
.mailpoet_text_label,
.mailpoet_textarea_label,
.mailpoet_select_label,
.mailpoet_radio_label,
.mailpoet_checkbox_label,
.mailpoet_list_label,
.mailpoet_date_label {
  display:block;
  font-weight: normal;
}

/* inputs */
.mailpoet_text,
.mailpoet_textarea,
.mailpoet_select,
.mailpoet_date_month,
.mailpoet_date_day,
.mailpoet_date_year,
.mailpoet_date {
  display:block;
}

.mailpoet_text,
.mailpoet_textarea {
  width: 200px;
}

.mailpoet_checkbox {
}

.mailpoet_submit {
}

.mailpoet_divider {
}

.mailpoet_message {
}

.mailpoet_form_loading {
  width: 30px;
  text-align: center;
  line-height: normal;
}

.mailpoet_form_loading > span {
  width: 5px;
  height: 5px;
  background-color: #5b5b5b;
}
EOL;
  }
}
