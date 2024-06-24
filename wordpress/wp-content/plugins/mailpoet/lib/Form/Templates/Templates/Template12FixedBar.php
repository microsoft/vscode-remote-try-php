<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Templates\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\Templates\FormTemplate;

class Template12FixedBar extends FormTemplate {
  const ID = 'template_12_fixed_bar';

  /** @var string */
  protected $assetsDirectory = 'template-12';

  public function getName(): string {
    return _x('Limited Time Offer', 'Form template name', 'mailpoet');
  }

  public function getThumbnailUrl(): string {
    return $this->getAssetUrl('fixedbar.png');
  }

  public function getBody(): array {
    return [
      [
        'type' => 'divider',
        'params' => [
          'class_name' => '',
          'height' => '1',
          'type' => 'spacer',
          'style' => 'solid',
          'divider_height' => '1',
          'divider_width' => '100',
          'color' => 'black',
        ],
        'id' => 'divider',
        'name' => 'Divider',
      ],
      [
        'type' => 'columns',
        'body' => [
          [
            'type' => 'column',
            'params' => [
              'class_name' => '',
              'vertical_alignment' => '',
              'width' => '25',
            ],
            'body' => [
              [
                'type' => 'heading',
                'id' => 'heading',
                'params' => [
                  'content' => '<strong>' . _x('DEAL<span style="color:#e04f8e" class="has-inline-color">WEEK</span>', 'Text in a web form. Keep HTML tags!', 'mailpoet') . '</strong>',
                  'level' => '2',
                  'align' => 'left',
                  'font_size' => '36',
                  'text_color' => '',
                  'background_color' => '',
                  'anchor' => '',
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
              'width' => '25',
            ],
            'body' => [
              [
                'type' => 'text',
                'params' => [
                  'label' => _x('Full Name', 'Form label', 'mailpoet'),
                  'class_name' => '',
                  'label_within' => '1',
                ],
                'id' => 'first_name',
                'name' => 'First name',
                'styles' => [
                  'full_width' => '1',
                  'bold' => '0',
                  'background_color' => '#ffffff',
                  'font_color' => '#000000',
                  'border_size' => '1',
                  'border_radius' => '0',
                  'border_color' => '#313131',
                ],
              ],
            ],
          ],
          [
            'type' => 'column',
            'params' => [
              'class_name' => '',
              'vertical_alignment' => '',
              'width' => '25',
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
                  'font_color' => '#000000',
                  'border_size' => '1',
                  'border_radius' => '0',
                  'border_color' => '#313131',
                ],
              ],
            ],
          ],
          [
            'type' => 'column',
            'params' => [
              'class_name' => '',
              'vertical_alignment' => '',
              'width' => '25',
            ],
            'body' => [
              [
                'type' => 'submit',
                'params' => [
                  'label' => _x('GET 20% OFF', 'Form label', 'mailpoet'),
                  'class_name' => '',
                ],
                'id' => 'submit',
                'name' => 'Submit',
                'styles' => [
                  'full_width' => '1',
                  'bold' => '1',
                  'background_color' => '#e04f8e',
                  'font_size' => '16',
                  'font_color' => '#ffffff',
                  'border_size' => '0',
                  'border_radius' => '5',
                  'border_color' => '#313131',
                  'padding' => '10',
                  'font_family' => 'Cairo',
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
      [
        'type' => 'columns',
        'body' => [
          [
            'type' => 'column',
            'params' => [
              'class_name' => '',
              'vertical_alignment' => '',
              'width' => '65',
            ],
            'body' => [
              [
                'type' => 'paragraph',
                'id' => 'paragraph',
                'params' => [
                  'content' => '<span style="font-family: Cairo" data-font="Cairo" class="mailpoet-has-font">' . _x('<strong>SUBSCRIBE</strong> AND <strong><span style="color:#e04f8e" class="has-inline-color">GET 20% OFF</span></strong> YOUR NEXT ORDER! <strong>OFFER ENDS SOON</strong> - DON’T MISS OUT!', 'Text in a web form. Keep HTML tags!', 'mailpoet') . '</span>',
                  'drop_cap' => '0',
                  'align' => 'left',
                  'font_size' => '17',
                  'text_color' => '#1e1e1e',
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
              'width' => '35',
            ],
            'body' => [
              [
                'type' => 'paragraph',
                'id' => 'paragraph',
                'params' => [
                  'content' => '<span style="font-family: Cairo" data-font="Cairo" class="mailpoet-has-font">' . $this->replacePrivacyLinkTags(_x('We don’t spam! Read our [link]privacy policy[/link] for more info.', 'Text in a web form.', 'mailpoet'), '#') . '</span>',
                  'drop_cap' => '0',
                  'align' => 'right',
                  'font_size' => '15',
                  'text_color' => '#1e1e1e',
                  'background_color' => '',
                  'class_name' => '',
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
        'fixed_bar' => [
          'enabled' => '1',
          'styles' => [
            'width' => [
              'unit' => 'pixel',
              'value' => '1100',
            ],
          ],
          'position' => 'top',
          'animation' => 'slideup',
          'posts' => [
            'all' => '',
          ],
          'pages' => [
            'all' => '',
          ],
        ],
        'below_posts' => ['enabled' => ''],
        'slide_in' => ['enabled' => ''],
        'others' => [],
      ],
      'border_radius' => '0',
      'border_size' => '0',
      'form_padding' => '0',
      'input_padding' => '10',
      'font_family' => 'Cairo',
      'close_button' => 'square_black',
      'success_validation_color' => '#00d084',
      'error_validation_color' => '#cf2e2e',
      'fontSize' => '15',
      'fontColor' => '#1e1e1e',
      'gradient' => 'linear-gradient(230deg,rgba(255,159,218,0.7) 0%,rgb(255,255,255) 41%)',
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

::placeholder {
  color: black;
}

/* columns */
.mailpoet_column_with_background {
  padding: 0px;
}

/* space between columns */
.mailpoet_form_column:not(:first-child) {
  margin-left: 20px;
}

/* input wrapper (label + input) */
.mailpoet_paragraph {
  line-height:20px;
  margin-bottom: 5px;
}

h2.mailpoet-heading {
  margin: 0 0 20px 0;
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
