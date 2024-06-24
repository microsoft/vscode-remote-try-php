<?php

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
use MailPoetVendor\Twig\Extension\CoreExtension;
use MailPoetVendor\Twig\Extension\SandboxExtension;
use MailPoetVendor\Twig\Markup;
use MailPoetVendor\Twig\Sandbox\SecurityError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedTagError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFilterError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFunctionError;
use MailPoetVendor\Twig\Source;
use MailPoetVendor\Twig\Template;

/* newsletter/templates/blocks/automatedLatestContentLayout/settings.hbs */
class __TwigTemplate_b986e1f134b168c46f6aef00c51014ec3a7305fa36d3cd4849935a1999818651 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        yield "<h3>";
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Post selection");
        yield "</h3>

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 4
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show max:");
        yield "</div>
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"text\" class=\"mailpoet_input mailpoet_input_small mailpoet_automated_latest_content_show_amount\" value=\"{{ model.amount }}\" maxlength=\"2\" size=\"2\" data-automation-id=\"show_max_posts\" />
        <select class=\"mailpoet_select mailpoet_select_large mailpoet_automated_latest_content_content_type\">
            <option value=\"post\" {{#ifCond model.contentType '==' 'post'}}SELECTED{{/ifCond}}>";
        // line 8
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Posts");
        yield "</option>
            <option value=\"page\" {{#ifCond model.contentType '==' 'page'}}SELECTED{{/ifCond}}>";
        // line 9
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Pages");
        yield "</option>
            <option value=\"mailpoet_page\" {{#ifCond model.contentType '==' 'mailpoet_page'}}SELECTED{{/ifCond}}>";
        // line 10
        yield $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet pages");
        yield "</option>
        </select>
    </div>
</div>

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_select_option\">
        <select class=\"mailpoet_select mailpoet_automated_latest_content_categories_and_tags\" multiple=\"multiple\">
          {{#each model.terms}}
            <option value=\"{{ id }}\" selected=\"selected\">{{ text }}</option>
          {{/each}}
        </select>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_automated_latest_content_include_or_exclude\" class=\"mailpoet_automated_latest_content_include_or_exclude\" value=\"include\" {{#ifCond model.inclusionType '==' 'include'}}CHECKED{{/ifCond}}/>
            ";
        // line 26
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Include");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_automated_latest_content_include_or_exclude\" class=\"mailpoet_automated_latest_content_include_or_exclude\" value=\"exclude\" {{#ifCond model.inclusionType '==' 'exclude'}}CHECKED{{/ifCond}} />
            ";
        // line 32
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Exclude");
        yield "
        </label>
    </div>
</div>

<hr class=\"mailpoet_separator\" />


<div class=\"mailpoet_form_field\">
    <a href=\"javascript:;\" class=\"mailpoet_automated_latest_content_show_display_options\" data-automation-id=\"display_options\">
      {{#if _displayOptionsHidden}}
        ";
        // line 43
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Display options");
        yield "
      {{else}}
        ";
        // line 45
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Hide display options");
        yield "
      {{/if}}
    </a>
</div>
<div class=\"mailpoet_automated_latest_content_display_options {{#if _displayOptionsHidden}}mailpoet_closed{{/if}}\">
    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_display_type\" class=\"mailpoet_automated_latest_content_display_type\" value=\"excerpt\" {{#ifCond model.displayType '==' 'excerpt'}}CHECKED{{/ifCond}}/>
                ";
        // line 54
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Excerpt");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_display_type\" class=\"mailpoet_automated_latest_content_display_type\" value=\"full\" {{#ifCond model.displayType '==' 'full'}}CHECKED{{/ifCond}}/>
                ";
        // line 60
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Full post");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_display_type\" class=\"mailpoet_automated_latest_content_display_type\" value=\"titleOnly\" {{#ifCond model.displayType '==' 'titleOnly'}}CHECKED{{/ifCond}} />
                ";
        // line 66
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title only");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title\">";
        // line 72
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title Format");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"h1\" {{#ifCond model.titleFormat '==' 'h1'}}CHECKED{{/ifCond}}/>
                ";
        // line 76
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 1");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"h2\" {{#ifCond model.titleFormat '==' 'h2'}}CHECKED{{/ifCond}}/>
                ";
        // line 82
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 2");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"h3\" {{#ifCond model.titleFormat '==' 'h3'}}CHECKED{{/ifCond}}/>
                ";
        // line 88
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 3");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option mailpoet_automated_latest_content_title_as_list {{#ifCond model.displayType '!=' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"ul\" {{#ifCond model.titleFormat '==' 'ul'}}CHECKED{{/ifCond}}/>
                ";
        // line 94
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show as list");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title\">";
        // line 100
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title Alignment");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_alignment\" class=\"mailpoet_automated_latest_content_title_alignment\" value=\"left\" {{#ifCond model.titleAlignment '==' 'left'}}CHECKED{{/ifCond}} />
                ";
        // line 104
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_alignment\" class=\"mailpoet_automated_latest_content_title_alignment\" value=\"center\" {{#ifCond model.titleAlignment '==' 'center'}}CHECKED{{/ifCond}} />
                ";
        // line 110
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Center");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_alignment\" class=\"mailpoet_automated_latest_content_title_alignment\" value=\"right\" {{#ifCond model.titleAlignment '==' 'right'}}CHECKED{{/ifCond}} />
                ";
        // line 116
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field mailpoet_automated_latest_content_title_as_link {{#ifCond model.titleFormat '===' 'ul'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 122
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title as links");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_as_links\" class=\"mailpoet_automated_latest_content_title_as_links\" value=\"true\" {{#if model.titleIsLink}}CHECKED{{/if}}/>
                ";
        // line 126
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_as_links\" class=\"mailpoet_automated_latest_content_title_as_links\" value=\"false\" {{#unless model.titleIsLink}}CHECKED{{/unless}}/>
                ";
        // line 132
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
            </label>
        </div>
    </div>

    <hr class=\"mailpoet_separator mailpoet_automated_latest_content_title_position_separator {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\" />

    <div class=\"mailpoet_form_field mailpoet_automated_latest_content_title_position {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 140
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Title position", "Setting in the email designer to position the blog post title");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input data-automation-id=\"title_above_post\" type=\"radio\" name=\"mailpoet_automated_latest_content_title_position\" class=\"mailpoet_automated_latest_content_title_position\" value=\"abovePost\" {{#ifCond model.titlePosition '!=' 'aboveExcerpt'}}CHECKED{{/ifCond}}/>
                ";
        // line 144
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Above the post", "Display the post title above the post block");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input data-automation-id=\"title_above_excerpt\" type=\"radio\" name=\"mailpoet_automated_latest_content_title_position\" class=\"mailpoet_automated_latest_content_title_position\" value=\"aboveExcerpt\" {{#ifCond model.titlePosition '==' 'aboveExcerpt'}}CHECKED{{/ifCond}}/>
                ";
        // line 150
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Above the excerpt text", "Display the post title above the post excerpt");
        yield "
            </label>
        </div>
    </div>

    <hr class=\"mailpoet_separator mailpoet_automated_latest_content_image_separator {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\" />

    <div class=\"mailpoet_form_field mailpoet_automated_latest_content_featured_image_position_container {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 158
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Featured image position");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"centered\" {{#ifCond _featuredImagePosition '==' 'centered' }}CHECKED{{/ifCond}}/>
                ";
        // line 162
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Centered");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"left\" {{#ifCond _featuredImagePosition '==' 'left' }}CHECKED{{/ifCond}}/>
                ";
        // line 168
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"right\" {{#ifCond _featuredImagePosition '==' 'right' }}CHECKED{{/ifCond}}/>
                ";
        // line 174
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"alternate\" {{#ifCond _featuredImagePosition '==' 'alternate' }}CHECKED{{/ifCond}}/>
                ";
        // line 180
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Alternate");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"none\" {{#ifCond _featuredImagePosition '==' 'none' }}CHECKED{{/ifCond}}/>
                ";
        // line 186
        yield $this->extensions['MailPoet\Twig\I18n']->translate("None");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_automated_latest_content_non_title_list_options {{#ifCond model.displayType '==' 'titleOnly'}}{{#ifCond model.titleFormat '==' 'ul'}}mailpoet_hidden{{/ifCond}}{{/ifCond}}\">
        <div class=\"mailpoet_form_field mailpoet_automated_latest_content_image_full_width_option {{#ifCond model.displayType '==' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
            <div class=\"mailpoet_form_field_title\">";
        // line 193
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Image width");
        yield "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_automated_latest_content_image_full_width\" value=\"true\" {{#if model.imageFullWidth}}CHECKED{{/if}}/>
                    ";
        // line 197
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Full width");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_automated_latest_content_image_full_width\" value=\"false\" {{#unless model.imageFullWidth}}CHECKED{{/unless}}/>
                    ";
        // line 203
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Padded");
        yield "
                </label>
            </div>
        </div>

        <hr class=\"mailpoet_separator\" />

        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title\">";
        // line 211
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show author");
        yield "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_author\" class=\"mailpoet_automated_latest_content_show_author\" value=\"no\" {{#ifCond model.showAuthor '==' 'no'}}CHECKED{{/ifCond}}/>
                    ";
        // line 215
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_author\" class=\"mailpoet_automated_latest_content_show_author\" value=\"aboveText\" {{#ifCond model.showAuthor '==' 'aboveText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 221
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Above text");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_author\" class=\"mailpoet_automated_latest_content_show_author\" value=\"belowText\" {{#ifCond model.showAuthor '==' 'belowText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 227
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Below text");
        yield "<br />
                </label>
            </div>
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 230
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Preceded by:");
        yield "</div>
            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_automated_latest_content_author_preceded_by\" value=\"{{ model.authorPrecededBy }}\" />
            </div>
        </div>

        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title\">";
        // line 237
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show categories");
        yield "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_categories\" class=\"mailpoet_automated_latest_content_show_categories\" value=\"no\" {{#ifCond model.showCategories '==' 'no'}}CHECKED{{/ifCond}}/>
                    ";
        // line 241
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_categories\" class=\"mailpoet_automated_latest_content_show_categories\" value=\"aboveText\" {{#ifCond model.showCategories '==' 'aboveText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 247
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Above text");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_categories\" class=\"mailpoet_automated_latest_content_show_categories\" value=\"belowText\" {{#ifCond model.showCategories '==' 'belowText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 253
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Below text");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 256
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Preceded by:");
        yield "</div>
            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_automated_latest_content_categories\" value=\"{{ model.categoriesPrecededBy }}\" />
            </div>
        </div>

        <hr class=\"mailpoet_separator\" />

        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 265
        yield $this->extensions['MailPoet\Twig\I18n']->translate("\"Read more\" text");
        yield "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_read_more_type\" class=\"mailpoet_automated_latest_content_read_more_type\" value=\"link\" {{#ifCond model.readMoreType '==' 'link'}}CHECKED{{/ifCond}}/>
                    ";
        // line 269
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Link");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_read_more_type\" class=\"mailpoet_automated_latest_content_read_more_type\" value=\"button\" {{#ifCond model.readMoreType '==' 'button'}}CHECKED{{/ifCond}}/>
                    ";
        // line 275
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Button");
        yield "
                </label>
            </div>

            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_automated_latest_content_read_more_text {{#ifCond model.readMoreType '!=' 'link'}}mailpoet_hidden{{/ifCond}}\" value=\"{{ model.readMoreText }}\" />
            </div>

            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <a href=\"javascript:;\" class=\"mailpoet_automated_latest_content_select_button {{#ifCond model.readMoreType '!=' 'button'}}mailpoet_hidden{{/ifCond}}\">";
        // line 284
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Design a button");
        yield "</a>
            </div>
        </div>

        <hr class=\"mailpoet_separator\" />
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 292
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Sort by");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_sort_by\" class=\"mailpoet_automated_latest_content_sort_by\" value=\"newest\" {{#ifCond model.sortBy '==' 'newest'}}CHECKED{{/ifCond}}/>
                ";
        // line 296
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Newest");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_sort_by\" class=\"mailpoet_automated_latest_content_sort_by\" value=\"oldest\" {{#ifCond model.sortBy '==' 'oldest'}}CHECKED{{/ifCond}}/>
                ";
        // line 302
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Oldest");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_automated_latest_content_non_title_list_options {{#ifCond model.displayType '==' 'titleOnly'}}{{#ifCond model.titleFormat '==' 'ul'}}mailpoet_hidden{{/ifCond}}{{/ifCond}}\">
        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 309
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show divider between posts");
        yield "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
            <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_divider\"class=\"mailpoet_automated_latest_content_show_divider\" value=\"true\" {{#if model.showDivider}}CHECKED{{/if}}/>
                    ";
        // line 313
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_divider\"class=\"mailpoet_automated_latest_content_show_divider\" value=\"false\" {{#unless model.showDivider}}CHECKED{{/unless}}/>
                    ";
        // line 319
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
                </label>
            </div>
            <div>
                <a href=\"javascript:;\" class=\"mailpoet_automated_latest_content_select_divider\">";
        // line 323
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Select divider");
        yield "</a>
            </div>
        </div>

    </div>
</div>

<div class=\"mailpoet_form_field\">
    <input type=\"button\" data-automation-id=\"alc_settings_done\" class=\"button button-primary mailpoet_done_editing\" value=\"";
        // line 331
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("Done"), "html_attr");
        yield "\" />
</div>

";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/blocks/automatedLatestContentLayout/settings.hbs";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  540 => 331,  529 => 323,  522 => 319,  513 => 313,  506 => 309,  496 => 302,  487 => 296,  480 => 292,  469 => 284,  457 => 275,  448 => 269,  441 => 265,  429 => 256,  423 => 253,  414 => 247,  405 => 241,  398 => 237,  388 => 230,  382 => 227,  373 => 221,  364 => 215,  357 => 211,  346 => 203,  337 => 197,  330 => 193,  320 => 186,  311 => 180,  302 => 174,  293 => 168,  284 => 162,  277 => 158,  266 => 150,  257 => 144,  250 => 140,  239 => 132,  230 => 126,  223 => 122,  214 => 116,  205 => 110,  196 => 104,  189 => 100,  180 => 94,  171 => 88,  162 => 82,  153 => 76,  146 => 72,  137 => 66,  128 => 60,  119 => 54,  107 => 45,  102 => 43,  88 => 32,  79 => 26,  60 => 10,  56 => 9,  52 => 8,  45 => 4,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/automatedLatestContentLayout/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/automatedLatestContentLayout/settings.hbs");
    }
}
