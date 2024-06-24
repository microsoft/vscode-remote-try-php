<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class NewsletterBlank13Column {

  private $assets_url;
  private $external_template_image_url;
  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->assets_url = $assets_url;
    $this->external_template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/newsletter-blank-1-3-column';
    $this->template_image_url = $this->assets_url . '/img/blank_templates';
    $this->social_icon_url = $this->assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Newsletter: Blank 1:3 Column", 'mailpoet'),
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
                "backgroundColor" => "#f8f8f8",
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
                    "type" => "header",
                    "text" => '<a href="[link:newsletter_view_in_browser_url]">' . __("View this in your browser.", 'mailpoet') . '</a>',
                    "styles" => [
                      "block" => [
                        "backgroundColor" => "transparent",
                      ],
                      "text" => [
                        "fontColor" => "#222222",
                        "fontFamily" => "Arial",
                        "fontSize" => "12px",
                        "textAlign" => "center",
                      ],
                      "link" => [
                        "fontColor" => "#6cb7d4",
                        "textDecoration" => "underline",
                      ],
                    ],
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
                    "text" => __("<h1 style=\"text-align: center;\"><strong>Let's Get Started! </strong></h1>\n<p>&nbsp;</p>\n<p>It's time to design your newsletter! In the right sidebar, you'll find four menu items that will help you customize your newsletter:</p>\n<ol>\n<li>Content</li>\n<li>Columns</li>\n<li>Styles</li>\n<li>Preview</li>\n</ol>", 'mailpoet'),
                  ],
                  [
                    "type" => "divider",
                    "styles" => [
                      "block" => [
                        "backgroundColor" => "transparent",
                        "padding" => "13px",
                        "borderStyle" => "dotted",
                        "borderWidth" => "3px",
                        "borderColor" => "#aaaaaa",
                      ],
                    ],
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
                "backgroundColor" => "transparent",
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
                    "type" => "text",
                    "text" => '<h3>' . __('This template...', 'mailpoet') . '</h3>',
                  ],
                  [
                    "type" => "text",
                    "text" => '<p>' . __('In the right sidebar, you can add layout blocks to your newsletter.', 'mailpoet') . '</p>',
                  ],
                ],
              ],
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
                    "type" => "text",
                    "text" => '<h3>' . __('... has a...', 'mailpoet') . '</h3>',
                  ],
                  [
                    "type" => "text",
                    "text" => __("<p>You have the choice of:</p>\n<ul>\n<li>1 column</li>\n<li>2 columns</li>\n<li>3 columns</li>\n</ul>", 'mailpoet'),
                  ],
                ],
              ],
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
                    "type" => "text",
                    "text" => '<h3>' . __('3-column layout.', 'mailpoet') . '</h3>',
                  ],
                  [
                    "type" => "text",
                    "text" => '<p>' . __('You can add as many layout blocks as you want!', 'mailpoet') . '</p>',
                  ],
                  [
                    "type" => "text",
                    "text" => "",
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
                "backgroundColor" => "#f8f8f8",
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
                    "type" => "divider",
                    "styles" => [
                      "block" => [
                        "backgroundColor" => "transparent",
                        "padding" => "24.5px",
                        "borderStyle" => "solid",
                        "borderWidth" => "3px",
                        "borderColor" => "#aaaaaa",
                      ],
                    ],
                  ],
                  [
                    "type" => "social",
                    "iconSet" => "grey",
                    "icons" => [
                      [
                        "type" => "socialIcon",
                        "iconType" => "facebook",
                        "link" => "http://www.facebook.com",
                        "image" => $this->social_icon_url . "/02-grey/Facebook.png",
                        "height" => "32px",
                        "width" => "32px",
                        "text" => "Facebook",
                      ],
                      [
                        "type" => "socialIcon",
                        "iconType" => "twitter",
                        "link" => "http://www.twitter.com",
                        "image" => $this->social_icon_url . "/02-grey/Twitter.png",
                        "height" => "32px",
                        "width" => "32px",
                        "text" => "Twitter",
                      ],
                    ],
                  ],
                  [
                    "type" => "divider",
                    "styles" => [
                      "block" => [
                        "backgroundColor" => "transparent",
                        "padding" => "7.5px",
                        "borderStyle" => "solid",
                        "borderWidth" => "3px",
                        "borderColor" => "#aaaaaa",
                      ],
                    ],
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
                        "textAlign" => "center",
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
          "fontSize" => "16px",
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
          "backgroundColor" => "#eeeeee",
        ],
      ],
    ];
  }

  private function getThumbnail() {
    return $this->external_template_image_url . '/thumbnail.20190930.jpg';
  }
}
