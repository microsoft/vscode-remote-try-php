<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Templates\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\Templates\FormTemplate;

class Template7Widget extends FormTemplate {
  const ID = 'template-7-widget';

  /** @var string */
  protected $assetsDirectory = 'template-7';

  public function getName(): string {
    return _x('Latest Deals', 'Form template name', 'mailpoet');
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
          'color' => 'black',
        ],
        'id' => 'divider',
        'name' => 'Divider',
      ],
      [
        'type' => 'heading',
        'id' => 'heading',
        'params' => [
          'content' => $this->wp->wpStaticizeEmoji('🕶️') . ' <strong><span style="font-family: Abril FatFace" data-font="Abril FatFace" class="mailpoet-has-font">' . _x('Relax!', 'Text in a web form.', 'mailpoet') . '</span></strong>',
          'level' => '2',
          'align' => 'center',
          'font_size' => '40',
          'text_color' => '#ffffff',
          'line_height' => '1.2',
          'background_color' => '',
          'anchor' => '',
          'class_name' => '',
        ],
      ],
      [
        'type' => 'heading',
        'id' => 'heading',
        'params' => [
          'content' => '<span style="font-family: Cairo" data-font="Cairo" class="mailpoet-has-font">' . _x('Put your feet up and let us do the hard work for you. Sign up to receive our latest deals directly in your inbox.', 'Text in a web form.', 'mailpoet') . '</span>',
          'level' => '2',
          'align' => 'center',
          'font_size' => '16',
          'text_color' => '#ffffff',
          'line_height' => '1.2',
          'background_color' => '',
          'anchor' => '',
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
          'background_color' => '#ffffff',
          'font_color' => '#abb8c3',
          'border_size' => '0',
          'border_radius' => '6',
        ],
      ],
      [
        'type' => 'submit',
        'params' => [
          'label' => _x('Get the latest deals', 'Form label', 'mailpoet'),
          'class_name' => '',
        ],
        'id' => 'submit',
        'name' => 'Submit',
        'styles' => [
          'full_width' => '1',
          'bold' => '1',
          'gradient' => 'linear-gradient(180deg,rgb(0,159,251) 0%,rgb(29,123,164) 100%)',
          'font_size' => '16',
          'font_color' => '#ffffff',
          'border_size' => '1',
          'border_radius' => '6',
          'padding' => '11',
          'font_family' => 'Cairo',
        ],
      ],
      [
        'type' => 'paragraph',
        'id' => 'paragraph',
        'params' => [
          'content' => '<span style="font-family: Cairo" data-font="Cairo" class="mailpoet-has-font">' . $this->replacePrivacyLinkTags(_x('We’ll never send you spam or share your email address.<br>Find out more in our [link]Privacy Policy[/link].', 'Text in a web form. Keep HTML tags!', 'mailpoet'), '#') . '</span>',
          'drop_cap' => '0',
          'align' => 'center',
          'font_size' => '13',
          'line_height' => '1.5',
          'text_color' => '#ffffff',
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
          'color' => 'black',
        ],
        'id' => 'divider',
        'name' => 'Divider',
      ],
    ];
  }

  public function getSettings(): array {
    return [
      'success_message' => '',
      'segments' => [],
      'alignment' => 'left',
      'fontSize' => '14',
      'form_placement' => [
        'popup' => ['enabled' => ''],
        'below_posts' => ['enabled' => ''],
        'fixed_bar' => ['enabled' => ''],
        'slide_in' => ['enabled' => ''],
        'others' => [
          'styles' => [
            'width' => [
              'unit' => 'percent',
              'value' => '100',
            ],
          ],
        ],
      ],
      'border_radius' => '24',
      'border_size' => '0',
      'form_padding' => '20',
      'input_padding' => '12',
      'font_family' => 'Cairo',
      'background_image_url' => '',
      'background_image_display' => 'scale',
      'close_button' => 'round_white',
      'segments_selected_by' => 'admin',
      'gradient' => 'linear-gradient(180deg,rgb(255,233,112) 0%,rgb(230,174,70) 49%,rgb(228,37,111) 100%)',
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
    margin-bottom: 0px;
}

h2.mailpoet-heading {
    margin: -10px 0 10px 0;
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
