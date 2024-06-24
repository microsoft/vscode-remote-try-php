<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Templates\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\Templates\FormTemplate;

class Template18BelowPages extends FormTemplate {
  const ID = 'template_18_below_pages';

  /** @var string */
  protected $assetsDirectory = 'template-18';

  public function getName(): string {
    return _x('Black Friday', 'Form template name', 'mailpoet');
  }

  public function getThumbnailUrl(): string {
    return $this->getAssetUrl('belowpage.png');
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
              'vertical_alignment' => '',
              'width' => '50',
            ],
            'body' => [
              [
                'type' => 'paragraph',
                'id' => 'paragraph',
                'params' => [
                  'content' => '<strong><span style="font-family: Heebo" data-font="Heebo" class="mailpoet-has-font">' . _x('IT’S HERE! DON’T MISS OUT!', 'Text in a web form', 'mailpoet') . '</span></strong>',
                  'drop_cap' => '0',
                  'align' => 'center',
                  'font_size' => '20',
                  'text_color' => '',
                  'background_color' => '',
                  'class_name' => '',
                ],
              ],
              [
                'type' => 'divider',
                'params' => [
                  'class_name' => '',
                  'height' => '10',
                  'type' => 'divider',
                  'style' => 'solid',
                  'divider_height' => '10',
                  'divider_width' => '100',
                  'color' => '#ffffff',
                ],
                'id' => 'divider',
                'name' => 'Divider',
              ],
              [
                'type' => 'divider',
                'params' => [
                  'class_name' => '',
                  'height' => '1',
                  'type' => 'spacer',
                  'style' => 'solid',
                  'divider_height' => '1',
                  'divider_width' => '100',
                  'color' => '#185f70',
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
                      'width' => '50',
                    ],
                    'body' => [
                      [
                        'type' => 'heading',
                        'id' => 'heading',
                        'params' => [
                          'content' => '<strong><span style="font-family: Fjalla One" data-font="Fjalla One" class="mailpoet-has-font"><strong>' . _x('B L A C K', 'Text in a web form (Black Friday).', 'mailpoet') . '</strong></span></strong>',
                          'level' => '1',
                          'align' => 'center',
                          'font_size' => '32',
                          'text_color' => '#ffffff',
                          'background_color' => '',
                          'anchor' => 'block-heading_0.8430326562811867-1602517711078',
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
                      'width' => '50',
                    ],
                    'body' => [
                      [
                        'type' => 'heading',
                        'id' => 'heading',
                        'params' => [
                          'content' => '<strong><span style="font-family: Fjalla One" data-font="Fjalla One" class="mailpoet-has-font">' . _x('F R I D A Y', 'Text in a web form (Black Friday).', 'mailpoet') . '</span></strong>',
                          'level' => '1',
                          'align' => 'center',
                          'font_size' => '32',
                          'text_color' => '#ffffff',
                          'background_color' => '',
                          'anchor' => 'block-heading_0.8430326562811867-1602517711078',
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
              [
                'type' => 'divider',
                'params' => [
                  'class_name' => '',
                  'height' => '10',
                  'type' => 'divider',
                  'style' => 'solid',
                  'divider_height' => '10',
                  'divider_width' => '100',
                  'color' => '#ffffff',
                ],
                'id' => 'divider',
                'name' => 'Divider',
              ],
              [
                'type' => 'paragraph',
                'id' => 'paragraph',
                'params' => [
                  'content' => '<span style="font-family: Heebo" data-font="Heebo" class="mailpoet-has-font"><strong>' . _x('ENJOY 50% OFF ON ALL PRODUCTS', 'Text in a web form', 'mailpoet') . '<br></strong>' . _x('PLUS FREE SHIPPING = ORDERS OVER $100', 'Text in a web form', 'mailpoet') . '</span>',
                  'drop_cap' => '0',
                  'align' => 'center',
                  'font_size' => '15',
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
              'vertical_alignment' => '',
              'width' => '50',
            ],
            'body' => [
              [
                'type' => 'divider',
                'params' => [
                  'class_name' => '',
                  'height' => '50',
                  'type' => 'spacer',
                  'style' => 'solid',
                  'divider_height' => '1',
                  'divider_width' => '100',
                  'color' => '#185f70',
                ],
                'id' => 'divider',
                'name' => 'Divider',
              ],
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
                  'bold' => '1',
                  'background_color' => '#ffffff',
                  'font_color' => '#5b5e60',
                  'border_size' => '0',
                  'border_radius' => '2',
                ],
              ],
              [
                'type' => 'submit',
                'params' => [
                  'label' => _x('GET YOUR COUPON', 'Form label', 'mailpoet'),
                  'class_name' => '',
                ],
                'id' => 'submit',
                'name' => 'Submit',
                'styles' => [
                  'full_width' => '1',
                  'bold' => '1',
                  'background_color' => '#cf2e2e',
                  'font_size' => '15',
                  'font_color' => '#ffffff',
                  'border_size' => '0',
                  'border_radius' => '2',
                  'padding' => '10',
                  'font_family' => 'Heebo',
                ],
              ],
              [
                'type' => 'paragraph',
                'id' => 'paragraph',
                'params' => [
                  'content' => '<span style="font-family: Heebo" data-font="Heebo" class="mailpoet-has-font">' . $this->replacePrivacyLinkTags(_x('We don’t spam! Read our [link]privacy policy[/link] for more info.', 'Text in a web form.', 'mailpoet'), '#') . '</span>',
                  'drop_cap' => '0',
                  'align' => 'center',
                  'font_size' => '13',
                  'line_height' => '1.5',
                  'text_color' => '#ffffff',
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
      'border_radius' => '0',
      'border_size' => '0',
      'form_padding' => '40',
      'input_padding' => '10',
      'success_validation_color' => '#00d084',
      'error_validation_color' => '#cf2e2e',
      'close_button' => 'classic_white',
      'font_family' => 'Heebo',
      'fontSize' => '15',
      'form_placement' => [
        'popup' => [
          'enabled' => '',
        ],
        'fixed_bar' => [
          'enabled' => '',
        ],
        'below_posts' => [
          'enabled' => '1',
          'styles' => [
            'width' => [
              'unit' => 'percent',
              'value' => '100',
            ],
          ],
        ],
        'slide_in' => [
          'enabled' => '',
        ],
        'others' => [],
      ],
      'backgroundColor' => '#000000',
      'background_image_url' => $this->getAssetUrl('blackfriday-5.png'),
      'background_image_display' => 'scale',
      'fontColor' => '#ffffff',
      'border_color' => '#ffffff',
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

.wp-block-column:not(:first-child),
.mailpoet_form_column:not(:first-child) {
 padding: 0 20px;
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

.mailpoet_form_paragraph  last {
  margin-bottom: 0px;
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
    margin: 0 0 20px 0;
}

h1.mailpoet-heading {
	margin: 0 0 10px;
}
EOL;
  }
}
