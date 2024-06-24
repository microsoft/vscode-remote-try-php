<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Templates\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\Templates\FormTemplate;

class Template14FixedBar extends FormTemplate {
  const ID = 'template_14_fixed_bar';

  /** @var string */
  protected $assetsDirectory = 'template-14';

  public function getName(): string {
    return _x('Lifestyle Blog', 'Form template name', 'mailpoet');
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
              'vertical_alignment' => '',
              'width' => '10',
            ],
            'body' => [
              [
                'type' => 'image',
                'id' => 'image',
                'params' => [
                  'class_name' => 'is-style-default',
                  'align' => '',
                  'url' => $this->getAssetUrl('pic-1-682x1024.jpg'),
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
            ],
          ],
          [
            'type' => 'column',
            'params' => [
              'class_name' => '',
              'vertical_alignment' => '',
              'width' => '60',
            ],
            'body' => [
              [
                'type' => 'divider',
                'params' => [
                  'class_name' => '',
                  'height' => '10',
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
                  'content' => '<span style="font-family: Nothing You Could Do" data-font="Nothing You Could Do" class="mailpoet-has-font"><strong>' . _x('WANT MORE?', 'Text in a web form', 'mailpoet') . '</strong></span>', // @todo Add translations, links and emoji processing.,
                  'level' => '2',
                  'align' => 'center',
                  'font_size' => '24',
                  'text_color' => '',
                  'background_color' => '',
                  'anchor' => '',
                  'class_name' => '',
                ],
              ],
              [
                'type' => 'paragraph',
                'id' => 'paragraph',
                'params' => [
                  'content' => '<span style="font-family: Karla" data-font="Karla" class="mailpoet-has-font">' . _x('SIGN UP TO RECEIVE THE LATEST LIFESTYLE TIPS & TRICKS, PLUS SOME EXCLUSIVE GOODIES!', 'Text in a web form', 'mailpoet') . '</span><br><span style="font-family: Karla" data-font="Karla" class="mailpoet-has-font">' . $this->replacePrivacyLinkTags(_x('We don’t spam! Read our [link]privacy policy[/link] for more info.', 'Text in a web form.', 'mailpoet'), '#') . '</span>',
                  'drop_cap' => '0',
                  'align' => 'center',
                  'font_size' => '14',
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
              'width' => '30',
            ],
            'body' => [
              [
                'type' => 'divider',
                'params' => [
                  'class_name' => '',
                  'height' => '10',
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
                  'background_color' => '#faf6f1',
                  'border_size' => '0',
                  'border_radius' => '5',
                  'border_color' => '#313131',
                ],
              ],
              [
                'type' => 'submit',
                'params' => [
                  'label' => _x('LET’S DO THIS!', 'Form label', 'mailpoet'),
                  'class_name' => '',
                ],
                'id' => 'submit',
                'name' => 'Submit',
                'styles' => [
                  'full_width' => '1',
                  'bold' => '1',
                  'background_color' => '#edd4b5',
                  'font_size' => '15',
                  'font_color' => '#ffffff',
                  'border_size' => '0',
                  'border_radius' => '5',
                  'padding' => '10',
                  'font_family' => 'Karla',
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
        'popup' => [
          'enabled' => '',
        ],
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
        ],
        'below_posts' => [
          'enabled' => '',
        ],
        'slide_in' => [
          'enabled' => '',
        ],
        'others' => [],
      ],
      'border_radius' => '4',
      'border_size' => '0',
      'form_padding' => '0',
      'input_padding' => '10',
      'font_family' => 'Karla',
      'close_button' => 'classic',
      'success_validation_color' => '#00d084',
      'error_validation_color' => '#cf2e2e',
      'fontSize' => '15',
      'fontColor' => '#1e1e1e',
      'backgroundColor' => '#ffffff',
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
  padding: 0px;
}

.wp-block-column:not(:first-child),
.mailpoet_form_column:not(:first-child) {
 padding: 0 20px;
}

/* space between columns */
.mailpoet_form_column:not(:first-child) {
  margin-left: 0;
}

h2.mailpoet-heading {
  margin: 0 0 20px 0;
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
