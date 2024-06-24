<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Templates\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\Templates\FormTemplate;

class Template17SlideIn extends FormTemplate {
  const ID = 'template_17_slide_in';

  /** @var string */
  protected $assetsDirectory = 'template-17';

  public function getName(): string {
    return _x('Halloween', 'Form template name', 'mailpoet');
  }

  public function getThumbnailUrl(): string {
    return $this->getAssetUrl('slidein.png');
  }

  public function getBody(): array {
    return [
      [
        'type' => 'image',
        'id' => 'image',
        'params' => [
          'class_name' => 'is-style-default',
          'align' => '',
          'url' => $this->getAssetUrl('2714165.jpg'),
          'alt' => '',
          'title' => '',
          'caption' => '',
          'link_destination' => 'none',
          'link' => '',
          'href' => '',
          'link_class' => '',
          'rel' => '',
          'link_target' => '',
          'id' => '',
          'size_slug' => 'large',
          'width' => '',
          'height' => '',
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
              'width' => '10',
            ],
          ],
          [
            'type' => 'column',
            'params' => [
              'class_name' => '',
              'vertical_alignment' => '',
              'width' => '80',
            ],
            'body' => [
              [
                'type' => 'divider',
                'params' => [
                  'class_name' => '',
                  'height' => '20',
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
                'type' => 'heading',
                'id' => 'heading',
                'params' => [
                  'content' => '<span style="font-family: Sue Ellen Francisco" data-font="Sue Ellen Francisco" class="mailpoet-has-font"><strong>' . _x('BOO! DON’T BE SCARED!', 'Text in a web form', 'mailpoet') . '</strong></span>',
                  'level' => '1',
                  'align' => 'center',
                  'font_size' => '35',
                  'text_color' => '#ffffff',
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
                  'content' => '<span style="font-family: Oxygen" data-font="Oxygen" class="mailpoet-has-font">' . _x('There aren’t any tricks here, only treats!', 'Text in a web form.', 'mailpoet') . '<br>' . _x('Subscribe to claim your <strong><span style="color:#ce4e09" class="has-inline-color">exclusive Halloween</span></strong> offer from us.</span>', 'Text in a web form. Keep HTML tags!', 'mailpoet'),
                  'drop_cap' => '0',
                  'align' => 'center',
                  'font_size' => '15',
                  'line_height' => '1.5',
                  'text_color' => '',
                  'background_color' => '',
                  'class_name' => '',
                ],
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
                  'font_color' => '#595656',
                  'border_size' => '0',
                  'border_radius' => '4',
                ],
              ],
              [
                'type' => 'submit',
                'params' => [
                  'label' => _x('DARE TO SUBSCRIBE?!', 'Form label', 'mailpoet'),
                  'class_name' => '',
                ],
                'id' => 'submit',
                'name' => 'Submit',
                'styles' => [
                  'full_width' => '1',
                  'bold' => '1',
                  'background_color' => '#ce4e09',
                  'font_size' => '16',
                  'font_color' => '#ffffff',
                  'border_size' => '0',
                  'border_radius' => '4',
                  'padding' => '10',
                  'font_family' => 'Sue Ellen Francisco',
                ],
              ],
              [
                'type' => 'paragraph',
                'id' => 'paragraph',
                'params' => [
                  'content' => '<span style="font-family: Oxygen" data-font="Oxygen" class="mailpoet-has-font">' . $this->replacePrivacyLinkTags(_x('We don’t spam! Read our [link]privacy policy[/link] for more info.', 'Text in a web form.', 'mailpoet'), '#') . '</span>',
                  'drop_cap' => '0',
                  'align' => 'center',
                  'font_size' => '13',
                  'line_height' => '1.5',
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
                  'type' => 'spacer',
                  'style' => 'solid',
                  'divider_height' => '1',
                  'divider_width' => '100',
                  'color' => '#185f70',
                ],
                'id' => 'divider',
                'name' => 'Divider',
              ],
            ],
          ],
          [
            'type' => 'column',
            'params' => [
              'class_name' => '',
              'vertical_alignment' => '',
              'width' => '10',
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
      'border_radius' => '20',
      'border_size' => '0',
      'form_padding' => '0',
      'input_padding' => '10',
      'success_validation_color' => '#00d084',
      'error_validation_color' => '#cf2e2e',
      'close_button' => 'square_black',
      'font_family' => 'Oxygen',
      'fontSize' => '16',
      'form_placement' => [
        'popup' => [
          'enabled' => '',
        ],
        'fixed_bar' => [
          'enabled' => '',
        ],
        'below_posts' => [
          'enabled' => '',
        ],
        'slide_in' => [
          'enabled' => '1',
          'position' => 'right',
          'animation' => 'right',
          'styles' => [
            'width' => [
              'unit' => 'pixel',
              'value' => '420',
            ],
          ],
        ],
        'others' => [],
      ],
      'backgroundColor' => '#1a1a1a',
      'background_image_url' => '',
      'background_image_display' => 'scale',
      'fontColor' => '#ffffff',
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
