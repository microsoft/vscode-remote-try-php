<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config\PopulatorData\Templates;

if (!defined('ABSPATH')) exit;


class PostNotificationsBlank1Column {

  private $assets_url;
  private $external_template_image_url;
  private $template_image_url;
  private $social_icon_url;

  public function __construct(
    $assets_url
  ) {
    $this->assets_url = $assets_url;
    $this->external_template_image_url = 'https://ps.w.org/mailpoet/assets/newsletter-templates/post-notifications-blank-1-column';
    $this->template_image_url = $this->assets_url . '/img/blank_templates';
    $this->social_icon_url = $this->assets_url . '/img/newsletter_editor/social-icons';
  }

  public function get() {
    return [
      'name' => __("Post Notifications: Blank 1 Column", 'mailpoet'),
      'categories' => json_encode(['notification', 'blank']),
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
                    "alt" => "fake-logo",
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
                    "text" => __("<h1 style=\"text-align: center;\"><strong>Check Out Our New Blog Posts! </strong></h1>\n<p>&nbsp;</p>\n<p>MailPoet can <span style=\"line-height: 1.6em; background-color: inherit;\"><em>automatically</em> </span><span style=\"line-height: 1.6em; background-color: inherit;\">send your new blog posts to your subscribers.</span></p>\n<p><span style=\"line-height: 1.6em; background-color: inherit;\"></span></p>\n<p><span style=\"line-height: 1.6em; background-color: inherit;\">Below, you'll find three recent posts, which are displayed automatically, thanks to the <em>Automatic Latest Content</em> widget, which can be found in the right sidebar, under <em>Content</em>.</span></p>\n<p><span style=\"line-height: 1.6em; background-color: inherit;\"></span></p>\n<p><span style=\"line-height: 1.6em; background-color: inherit;\">To edit the settings and styles of your post, simply click on a post below.</span></p>", 'mailpoet'),
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
                  [
                    "type" => "spacer",
                    "styles" => [
                      "block" => [
                        "backgroundColor" => "transparent",
                        "height" => "40px",
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
          [
            "type" => "automatedLatestContentLayout",
            "withLayout" => true,
            "amount" => "3",
            "contentType" => "post",
            "terms" => [],
            "inclusionType" => "include",
            "displayType" => "excerpt",
            "titleFormat" => "h3",
            "titleAlignment" => "left",
            "titleIsLink" => false,
            "imageFullWidth" => false,
            "featuredImagePosition" => "alternate",
            "showAuthor" => "no",
            "authorPrecededBy" => __("Author:", 'mailpoet'),
            "showCategories" => "no",
            "categoriesPrecededBy" => __("Categories:", 'mailpoet'),
            "readMoreType" => "button",
            "readMoreText" => "Read more",
            "readMoreButton" => [
              "type" => "button",
              "text" => __("Read the post", 'mailpoet'),
              "url" => "[postLink]",
              "styles" => [
                "block" => [
                  "backgroundColor" => "#2ea1cd",
                  "borderColor" => "#0074a2",
                  "borderWidth" => "1px",
                  "borderRadius" => "5px",
                  "borderStyle" => "solid",
                  "width" => "160px",
                  "lineHeight" => "30px",
                  "fontColor" => "#ffffff",
                  "fontFamily" => "Verdana",
                  "fontSize" => "16px",
                  "fontWeight" => "normal",
                  "textAlign" => "center",
                ],
              ],
            ],
            "sortBy" => "newest",
            "showDivider" => true,
            "divider" => [
              "type" => "divider",
              "styles" => [
                "block" => [
                  "backgroundColor" => "transparent",
                  "padding" => "13px",
                  "borderStyle" => "solid",
                  "borderWidth" => "3px",
                  "borderColor" => "#aaaaaa",
                ],
              ],
            ],
            "backgroundColor" => "#ffffff",
            "backgroundColorAlternate" => "#eeeeee",
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
                    "type" => "spacer",
                    "styles" => [
                      "block" => [
                        "backgroundColor" => "transparent",
                        "height" => "40px",
                      ],
                    ],
                  ],
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
    return $this->external_template_image_url . '/thumbnail.20190411-1500.jpg';
  }
}
