<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Templates\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\Templates\FormTemplate;

class Template6FixedBar extends FormTemplate {
  const ID = 'template_6_fixed_bar';

  /** @var string */
  protected $assetsDirectory = 'template-6';

  public function getName(): string {
    return _x('Fitness Tips', 'Form template name', 'mailpoet');
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
              'width' => '70',
            ],
            'body' => [
              [
                'type' => 'heading',
                'id' => 'heading',
                'params' => [
                  'content' => '<span style="font-family: Montserrat" data-font="Montserrat" class="mailpoet-has-font">' . _x('<strong>Dive in!</strong> Start your journey today.', 'Text in a web form. Keep HTML tags!', 'mailpoet') . '</span>',
                  'level' => '2',
                  'align' => 'left',
                  'font_size' => '30',
                  'text_color' => '#38527a',
                  'line_height' => '1',
                  'background_color' => '',
                  'anchor' => '',
                  'class_name' => '',
                ],
              ],
              [
                'type' => 'paragraph',
                'id' => 'paragraph',
                'params' => [
                  'content' => '<strong><span style="font-family: Montserrat" data-font="Montserrat" class="mailpoet-has-font">' . $this->replacePrivacyLinkTags(_x('Sign up to start your fitness program. We promise we’ll never spam! Take a look at our [link]Privacy Policy[/link] for more info.', 'Text in a web form.', 'mailpoet'), '#') . '</span></strong>',
                  'drop_cap' => '0',
                  'align' => 'left',
                  'font_size' => '14',
                  'line_height' => '1.5',
                  'text_color' => '#38527a',
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
              'vertical_alignment' => '',
              'width' => '30',
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
                  'background_color' => '#f8fbff',
                  'font_color' => '#38527a',
                  'border_size' => '0',
                  'border_radius' => '10',
                ],
              ],
              [
                'type' => 'submit',
                'params' => [
                  'label' => _x('Get Started!', 'Form label', 'mailpoet'),
                  'class_name' => '',
                ],
                'id' => 'submit',
                'name' => 'Submit',
                'styles' => [
                  'full_width' => '1',
                  'bold' => '1',
                  'gradient' => 'linear-gradient(0deg,rgb(56,82,122) 0%,rgb(81,128,199) 100%)',
                  'font_size' => '19',
                  'font_color' => '#ffffff',
                  'border_size' => '0',
                  'border_radius' => '10',
                  'padding' => '10',
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
      'success_message' => '',
      'segments' => [],
      'alignment' => 'left',
      'fontSize' => '16',
      'form_placement' => [
        'popup' => ['enabled' => ''],
        'below_posts' => ['enabled' => ''],
        'fixed_bar' => [
          'enabled' => '1',
          'position' => 'top',
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
      'border_radius' => '0',
      'border_size' => '0',
      'form_padding' => '20',
      'input_padding' => '12',
      'font_family' => 'Montserrat',
      'background_image_url' => $this->getAssetUrl('form-bg.jpg'),
      'background_image_display' => 'scale',
      'close_button' => 'classic',
      'segments_selected_by' => 'admin',
      'success_validation_color' => '#00d084',
      'error_validation_color' => '#cf2e2e',
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

p.mailpoet_form_paragraph.last {
    margin-bottom: 5px;
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
h2.mailpoet-heading {
    margin-bottom: 20px;
    margin-top: 0;
}
EOL;
  }
}
