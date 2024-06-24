<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Templates\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\Templates\FormTemplate;

class Template4Widget extends FormTemplate {
  const ID = 'template_4_widget';

  /** @var string */
  protected $assetsDirectory = 'template-4';

  public function getName(): string {
    return _x('Newsletter Signup', 'Form template name', 'mailpoet');
  }

  public function getThumbnailUrl(): string {
    return $this->getAssetUrl('widget.png');
  }

  public function getBody(): array {
    return [
      [
        'type' => 'divider',
        'params' => [
          'class_name' => '',
          'height' => '10',
          'type' => 'spacer',
          'style' => 'solid',
          'divider_height' => '1',
          'divider_width' => '100',
          'color' => '#ffffff',
        ],
        'id' => 'divider',
        'name' => 'Divider',
      ],
      [
        'type' => 'heading',
        'id' => 'heading',
        'params' => [
          'content' => '<span style="font-family: Montserrat" data-font="Montserrat" class="mailpoet-has-font"><strong>' . _x('Oh hi there 👋<br>It’s nice to meet you.', 'Text in a web form. Keep HTML tags!', 'mailpoet') . '</strong></span>',
          'level' => '2',
          'align' => 'center',
          'font_size' => '20',
          'text_color' => '#0081ff',
          'line_height' => '1.5',
          'background_color' => '',
          'anchor' => '',
          'class_name' => '',
        ],
      ],
      [
        'type' => 'paragraph',
        'id' => 'paragraph',
        'params' => [
          'content' => '<strong><span style="font-family: Montserrat" data-font="Montserrat" class="mailpoet-has-font">' . _x('Sign up to receive awesome content in your inbox, every month.', 'Text in a web form.', 'mailpoet') . '</span></strong>',
          'drop_cap' => '0',
          'align' => 'center',
          'font_size' => '14',
          'line_height' => '1.5',
          'text_color' => '#000000',
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
          'bold' => '0',
          'background_color' => '#f1f1f1',
          'border_size' => '0',
          'border_radius' => '40',
          'border_color' => '#313131',
        ],
      ],
      [
        'type' => 'submit',
        'params' => [
          'label' => _x('Let’s keep in touch', 'Form label', 'mailpoet'),
          'class_name' => '',
        ],
        'id' => 'submit',
        'name' => 'Submit',
        'styles' => [
          'full_width' => '1',
          'bold' => '1',
          'background_color' => '#0081ff',
          'font_size' => '15',
          'font_color' => '#ffffff',
          'border_size' => '0',
          'border_radius' => '40',
          'border_color' => '#313131',
          'padding' => '15',
          'font_family' => 'Montserrat',
        ],
      ],
      [
        'type' => 'paragraph',
        'id' => 'paragraph',
        'params' => [
          'content' => '<em>' . $this->replacePrivacyLinkTags(_x('We don’t spam! Read our [link]privacy policy[/link] for more info.', 'Text in a web form.', 'mailpoet'), '#') . '</em>',
          'drop_cap' => '0',
          'align' => 'center',
          'font_size' => '13',
          'line_height' => '1.5',
          'text_color' => '',
          'background_color' => '',
          'class_name' => '',
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
      'fontSize' => '15',
      'form_placement' => [
        'popup' => ['enabled' => ''],
        'below_posts' => ['enabled' => ''],
        'fixed_bar' => ['enabled' => ''],
        'slide_in' => ['enabled' => ''],
        'others' => [
          'styles' => [
            'width' => [
              'value' => '100',
              'unit' => 'percent',
            ],
          ],
        ],
      ],
      'border_radius' => '0',
      'border_size' => '0',
      'form_padding' => '10',
      'input_padding' => '15',
      'font_family' => 'Montserrat',
      'close_button' => 'round_white',
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
