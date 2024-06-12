<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class WelcomeBlank1Column {

  private $assets_url;
  private $external_template_image_url;
  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->assets_url = $assets_url;
    $this->external_template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/welcome-email-blank-1-column';
    $this->template_image_url = $this->assets_url . '/img/blank_templates';
    $this->social_icon_url = $this->assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Welcome Email: Blank 1 Column", 'mailpoet'),
      'categories' => json_encode(['welcome', 'blank']),
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
                    "text" => __("<h1 style=\"text-align: center;\"><strong>Hi, new subscriber!</strong></h1>\n<p>&nbsp;</p>\n<p>[subscriber:firstname | default:Subscriber],</p>\n<p>&nbsp;</p>\n<p>You recently joined our list and we'd like to give you a warm welcome!</p>\n<p>&nbsp;</p>\n<p>Want to get to know us better? Check out some of our most popular articles: </p>\n<ol>\n<li><a href=\"https://www.mailpoet.com/blog/the-importance-of-focus-when-writing/\">The Importance of Focus When Writing</a></li>\n<li><a href=\"https://www.mailpoet.com/blog/writing-next-great-email-subject-line/\">How to Write a Great Subject Line</a></li>\n<li><a href=\"https://www.mailpoet.com/blog/write-advice-motivation/\">Just Sit Down and Write &ndash; Advice on Motivation from Ernest Hemingway</a></li>\n</ol>", 'mailpoet'),
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
                        "link" => "https://www.facebook.com",
                        "image" => $this->social_icon_url . "/02-grey/Facebook.png",
                        "height" => "32px",
                        "width" => "32px",
                        "text" => "Facebook",
                      ],
                      [
                        "type" => "socialIcon",
                        "iconType" => "twitter",
                        "link" => "https://www.twitter.com",
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
    return $this->external_template_image_url . '/thumbnail.20190411-1500.jpg';
  }
}
