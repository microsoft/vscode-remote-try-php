<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class SimpleText {

  private $assets_url;
  private $external_template_image_url;
  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->assets_url = $assets_url;
    $this->external_template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/simple-text';
    $this->template_image_url = $this->assets_url . '/img/blank_templates';
    $this->social_icon_url = $this->assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Simple Text", 'mailpoet'),
      'categories' => json_encode(['standard', 'blank']),
      'readonly' => 1,
      'thumbnail' => $this->getThumbnail(),
      'body' => json_encode($this->getBody()),
    ];
  }

  private function getBody() {
    return [
      "content" => [
        "type" => "container",
        "orientation" => "vertical",
        "styles" => [
          "block" => [
            "backgroundColor" => "transparent",
          ],
        ],
        "blocks" => [
          [
            "type" => "container",
            "orientation" => "horizontal",
            "styles" => [
              "block" => [
                "backgroundColor" => "#ffffff",
              ],
            ],
            "blocks" => [
              [
                "type" => "container",
                "orientation" => "vertical",
                "styles" => [
                  "block" => [
                    "backgroundColor" => "transparent",
                  ],
                ],
                "blocks" => [
                  [
                    "type" => "spacer",
                    "styles" => [
                      "block" => [
                        "backgroundColor" => "transparent",
                        "height" => "30px",
                      ],
                    ],
                  ],
                  [
                    "type" => "image",
                    "link" => "",
                    "src" => $this->template_image_url . "/fake-logo.png",
                    "alt" => __("Fake logo", 'mailpoet'),
                    "fullWidth" => false,
                    "width" => "598px",
                    "height" => "71px",
                    "styles" => [
                      "block" => [
                        "textAlign" => "center",
                      ],
                    ],
                  ],
                  [
                    "type" => "text",
                    "text" => __("<p style=\"text-align: left;\">Hi [subscriber:firstname | default:subscriber],</p>\n<p style=\"text-align: left;\"></p>\n<p style=\"text-align: left;\">In MailPoet, you can write emails in plain text, just like in a regular email. This can make your email newsletters more personal and attention-grabbing.</p>\n<p style=\"text-align: left;\"></p>\n<p style=\"text-align: left;\">Is this too simple? You can still style your text with basic formatting, like <strong>bold</strong> or <em>italics.</em></p>\n<p style=\"text-align: left;\"></p>\n<p style=\"text-align: left;\">Finally, you can also add a call-to-action button between 2 blocks of text, like this:</p>", 'mailpoet'),
                  ],
                ],
              ],
            ],
          ],
          [
            "type" => "container",
            "orientation" => "horizontal",
            "styles" => [
              "block" => [
                "backgroundColor" => "#ffffff",
              ],
            ],
            "blocks" => [
              [
                "type" => "container",
                "orientation" => "vertical",
                "styles" => [
                  "block" => [
                    "backgroundColor" => "transparent",
                  ],
                ],
                "blocks" => [
                  [
                    "type" => "spacer",
                    "styles" => [
                      "block" => [
                        "backgroundColor" => "transparent",
                        "height" => "23px",
                      ],
                    ],
                  ],
                  [
                    "type" => "button",
                    "text" => __("It's time to take action!", 'mailpoet'),
                    "url" => "",
                    "styles" => [
                      "block" => [
                        "backgroundColor" => "#2ea1cd",
                        "borderColor" => "#0074a2",
                        "borderWidth" => "1px",
                        "borderRadius" => "5px",
                        "borderStyle" => "solid",
                        "width" => "288px",
                        "lineHeight" => "40px",
                        "fontColor" => "#ffffff",
                        "fontFamily" => "Verdana",
                        "fontSize" => "16px",
                        "fontWeight" => "normal",
                        "textAlign" => "left",
                      ],
                    ],
                  ],
                  [
                    "type" => "text",
                    "text" => __("<p>Thanks for reading. See you soon!</p>\n<p>&nbsp;</p>\n<p><strong><em>The MailPoet Team</em></strong></p>", 'mailpoet'),
                  ],
                  [
                    "type" => "footer",
                    "text" => '<p><a href="[link:subscription_unsubscribe_url]">' . __("Unsubscribe", 'mailpoet') . '</a> | <a href="[link:subscription_manage_url]">' . __("Manage your subscription", 'mailpoet') . '</a><br />' . __("Add your postal address here!", 'mailpoet') . '</p>',
                    "styles" => [
                      "block" => [
                        "backgroundColor" => "transparent",
                      ],
                      "text" => [
                        "fontColor" => "#222222",
                        "fontFamily" => "Arial",
                        "fontSize" => "12px",
                        "textAlign" => "left",
                      ],
                      "link" => [
                        "fontColor" => "#6cb7d4",
                        "textDecoration" => "none",
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
        ],
      ],
      "globalStyles" => [
        "text" => [
          "fontColor" => "#000000",
          "fontFamily" => "Arial",
          "fontSize" => "15px",
        ],
        "h1" => [
          "fontColor" => "#111111",
          "fontFamily" => "Trebuchet MS",
          "fontSize" => "30px",
        ],
        "h2" => [
          "fontColor" => "#222222",
          "fontFamily" => "Trebuchet MS",
          "fontSize" => "24px",
        ],
        "h3" => [
          "fontColor" => "#333333",
          "fontFamily" => "Trebuchet MS",
          "fontSize" => "22px",
        ],
        "link" => [
          "fontColor" => "#21759B",
          "textDecoration" => "underline",
        ],
        "wrapper" => [
          "backgroundColor" => "#ffffff",
        ],
        "body" => [
          "backgroundColor" => "#ffffff",
        ],
      ],
    ];
  }

  private function getThumbnail() {
    return $this->external_template_image_url . '/thumbnail.20190411-1500.jpg';
  }
}
