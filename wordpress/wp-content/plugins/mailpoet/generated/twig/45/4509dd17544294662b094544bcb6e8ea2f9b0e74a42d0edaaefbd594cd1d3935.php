<?php

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
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
        echo "<h3>";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Post selection");
        echo "</h3>

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 4
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Show max:");
        echo "</div>
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"text\" class=\"mailpoet_input mailpoet_input_small mailpoet_automated_latest_content_show_amount\" value=\"{{ model.amount }}\" maxlength=\"2\" size=\"2\" data-automation-id=\"show_max_posts\" />
        <select class=\"mailpoet_select mailpoet_select_large mailpoet_automated_latest_content_content_type\">
            <option value=\"post\" {{#ifCond model.contentType '==' 'post'}}SELECTED{{/ifCond}}>";
        // line 8
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Posts");
        echo "</option>
            <option value=\"page\" {{#ifCond model.contentType '==' 'page'}}SELECTED{{/ifCond}}>";
        // line 9
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Pages");
        echo "</option>
            <option value=\"mailpoet_page\" {{#ifCond model.contentType '==' 'mailpoet_page'}}SELECTED{{/ifCond}}>";
        // line 10
        echo $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet pages");
        echo "</option>
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
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Include");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_automated_latest_content_include_or_exclude\" class=\"mailpoet_automated_latest_content_include_or_exclude\" value=\"exclude\" {{#ifCond model.inclusionType '==' 'exclude'}}CHECKED{{/ifCond}} />
            ";
        // line 32
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Exclude");
        echo "
        </label>
    </div>
</div>

<hr class=\"mailpoet_separator\" />


<div class=\"mailpoet_form_field\">
    <a href=\"javascript:;\" class=\"mailpoet_automated_latest_content_show_display_options\" data-automation-id=\"display_options\">
      {{#if _displayOptionsHidden}}
        ";
        // line 43
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Display options");
        echo "
      {{else}}
        ";
        // line 45
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Hide display options");
        echo "
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
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Excerpt");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_display_type\" class=\"mailpoet_automated_latest_content_display_type\" value=\"full\" {{#ifCond model.displayType '==' 'full'}}CHECKED{{/ifCond}}/>
                ";
        // line 60
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Full post");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_display_type\" class=\"mailpoet_automated_latest_content_display_type\" value=\"titleOnly\" {{#ifCond model.displayType '==' 'titleOnly'}}CHECKED{{/ifCond}} />
                ";
        // line 66
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Title only");
        echo "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title\">";
        // line 72
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Title Format");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"h1\" {{#ifCond model.titleFormat '==' 'h1'}}CHECKED{{/ifCond}}/>
                ";
        // line 76
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Heading 1");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"h2\" {{#ifCond model.titleFormat '==' 'h2'}}CHECKED{{/ifCond}}/>
                ";
        // line 82
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Heading 2");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"h3\" {{#ifCond model.titleFormat '==' 'h3'}}CHECKED{{/ifCond}}/>
                ";
        // line 88
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Heading 3");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option mailpoet_automated_latest_content_title_as_list {{#ifCond model.displayType '!=' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"ul\" {{#ifCond model.titleFormat '==' 'ul'}}CHECKED{{/ifCond}}/>
                ";
        // line 94
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Show as list");
        echo "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title\">";
        // line 100
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Title Alignment");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_alignment\" class=\"mailpoet_automated_latest_content_title_alignment\" value=\"left\" {{#ifCond model.titleAlignment '==' 'left'}}CHECKED{{/ifCond}} />
                ";
        // line 104
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_alignment\" class=\"mailpoet_automated_latest_content_title_alignment\" value=\"center\" {{#ifCond model.titleAlignment '==' 'center'}}CHECKED{{/ifCond}} />
                ";
        // line 110
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Center");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_alignment\" class=\"mailpoet_automated_latest_content_title_alignment\" value=\"right\" {{#ifCond model.titleAlignment '==' 'right'}}CHECKED{{/ifCond}} />
                ";
        // line 116
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        echo "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field mailpoet_automated_latest_content_title_as_link {{#ifCond model.titleFormat '===' 'ul'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 122
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Title as links");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_as_links\" class=\"mailpoet_automated_latest_content_title_as_links\" value=\"true\" {{#if model.titleIsLink}}CHECKED{{/if}}/>
                ";
        // line 126
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_as_links\" class=\"mailpoet_automated_latest_content_title_as_links\" value=\"false\" {{#unless model.titleIsLink}}CHECKED{{/unless}}/>
                ";
        // line 132
        echo $this->extensions['MailPoet\Twig\I18n']->translate("No");
        echo "
            </label>
        </div>
    </div>

    <hr class=\"mailpoet_separator mailpoet_automated_latest_content_title_position_separator {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\" />

    <div class=\"mailpoet_form_field mailpoet_automated_latest_content_title_position {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 140
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Title position", "Setting in the email designer to position the blog post title");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input data-automation-id=\"title_above_post\" type=\"radio\" name=\"mailpoet_automated_latest_content_title_position\" class=\"mailpoet_automated_latest_content_title_position\" value=\"abovePost\" {{#ifCond model.titlePosition '!=' 'aboveExcerpt'}}CHECKED{{/ifCond}}/>
                ";
        // line 144
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Above the post", "Display the post title above the post block");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input data-automation-id=\"title_above_excerpt\" type=\"radio\" name=\"mailpoet_automated_latest_content_title_position\" class=\"mailpoet_automated_latest_content_title_position\" value=\"aboveExcerpt\" {{#ifCond model.titlePosition '==' 'aboveExcerpt'}}CHECKED{{/ifCond}}/>
                ";
        // line 150
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Above the excerpt text", "Display the post title above the post excerpt");
        echo "
            </label>
        </div>
    </div>

    <hr class=\"mailpoet_separator mailpoet_automated_latest_content_image_separator {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\" />

    <div class=\"mailpoet_form_field mailpoet_automated_latest_content_featured_image_position_container {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 158
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Featured image position");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"centered\" {{#ifCond _featuredImagePosition '==' 'centered' }}CHECKED{{/ifCond}}/>
                ";
        // line 162
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Centered");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"left\" {{#ifCond _featuredImagePosition '==' 'left' }}CHECKED{{/ifCond}}/>
                ";
        // line 168
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"right\" {{#ifCond _featuredImagePosition '==' 'right' }}CHECKED{{/ifCond}}/>
                ";
        // line 174
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"alternate\" {{#ifCond _featuredImagePosition '==' 'alternate' }}CHECKED{{/ifCond}}/>
                ";
        // line 180
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Alternate");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"none\" {{#ifCond _featuredImagePosition '==' 'none' }}CHECKED{{/ifCond}}/>
                ";
        // line 186
        echo $this->extensions['MailPoet\Twig\I18n']->translate("None");
        echo "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_automated_latest_content_non_title_list_options {{#ifCond model.displayType '==' 'titleOnly'}}{{#ifCond model.titleFormat '==' 'ul'}}mailpoet_hidden{{/ifCond}}{{/ifCond}}\">
        <div class=\"mailpoet_form_field mailpoet_automated_latest_content_image_full_width_option {{#ifCond model.displayType '==' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
            <div class=\"mailpoet_form_field_title\">";
        // line 193
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Image width");
        echo "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_automated_latest_content_image_full_width\" value=\"true\" {{#if model.imageFullWidth}}CHECKED{{/if}}/>
                    ";
        // line 197
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Full width");
        echo "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_automated_latest_content_image_full_width\" value=\"false\" {{#unless model.imageFullWidth}}CHECKED{{/unless}}/>
                    ";
        // line 203
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Padded");
        echo "
                </label>
            </div>
        </div>

        <hr class=\"mailpoet_separator\" />

        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title\">";
        // line 211
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Show author");
        echo "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_author\" class=\"mailpoet_automated_latest_content_show_author\" value=\"no\" {{#ifCond model.showAuthor '==' 'no'}}CHECKED{{/ifCond}}/>
                    ";
        // line 215
        echo $this->extensions['MailPoet\Twig\I18n']->translate("No");
        echo "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_author\" class=\"mailpoet_automated_latest_content_show_author\" value=\"aboveText\" {{#ifCond model.showAuthor '==' 'aboveText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 221
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Above text");
        echo "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_author\" class=\"mailpoet_automated_latest_content_show_author\" value=\"belowText\" {{#ifCond model.showAuthor '==' 'belowText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 227
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Below text");
        echo "<br />
                </label>
            </div>
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 230
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Preceded by:");
        echo "</div>
            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_automated_latest_content_author_preceded_by\" value=\"{{ model.authorPrecededBy }}\" />
            </div>
        </div>

        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title\">";
        // line 237
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Show categories");
        echo "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_categories\" class=\"mailpoet_automated_latest_content_show_categories\" value=\"no\" {{#ifCond model.showCategories '==' 'no'}}CHECKED{{/ifCond}}/>
                    ";
        // line 241
        echo $this->extensions['MailPoet\Twig\I18n']->translate("No");
        echo "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_categories\" class=\"mailpoet_automated_latest_content_show_categories\" value=\"aboveText\" {{#ifCond model.showCategories '==' 'aboveText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 247
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Above text");
        echo "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_categories\" class=\"mailpoet_automated_latest_content_show_categories\" value=\"belowText\" {{#ifCond model.showCategories '==' 'belowText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 253
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Below text");
        echo "
                </label>
            </div>
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 256
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Preceded by:");
        echo "</div>
            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_automated_latest_content_categories\" value=\"{{ model.categoriesPrecededBy }}\" />
            </div>
        </div>

        <hr class=\"mailpoet_separator\" />

        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 265
        echo $this->extensions['MailPoet\Twig\I18n']->translate("\"Read more\" text");
        echo "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_read_more_type\" class=\"mailpoet_automated_latest_content_read_more_type\" value=\"link\" {{#ifCond model.readMoreType '==' 'link'}}CHECKED{{/ifCond}}/>
                    ";
        // line 269
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Link");
        echo "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_read_more_type\" class=\"mailpoet_automated_latest_content_read_more_type\" value=\"button\" {{#ifCond model.readMoreType '==' 'button'}}CHECKED{{/ifCond}}/>
                    ";
        // line 275
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Button");
        echo "
                </label>
            </div>

            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_automated_latest_content_read_more_text {{#ifCond model.readMoreType '!=' 'link'}}mailpoet_hidden{{/ifCond}}\" value=\"{{ model.readMoreText }}\" />
            </div>

            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <a href=\"javascript:;\" class=\"mailpoet_automated_latest_content_select_button {{#ifCond model.readMoreType '!=' 'button'}}mailpoet_hidden{{/ifCond}}\">";
        // line 284
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Design a button");
        echo "</a>
            </div>
        </div>

        <hr class=\"mailpoet_separator\" />
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 292
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Sort by");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_sort_by\" class=\"mailpoet_automated_latest_content_sort_by\" value=\"newest\" {{#ifCond model.sortBy '==' 'newest'}}CHECKED{{/ifCond}}/>
                ";
        // line 296
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Newest");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_sort_by\" class=\"mailpoet_automated_latest_content_sort_by\" value=\"oldest\" {{#ifCond model.sortBy '==' 'oldest'}}CHECKED{{/ifCond}}/>
                ";
        // line 302
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Oldest");
        echo "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_automated_latest_content_non_title_list_options {{#ifCond model.displayType '==' 'titleOnly'}}{{#ifCond model.titleFormat '==' 'ul'}}mailpoet_hidden{{/ifCond}}{{/ifCond}}\">
        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 309
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Show divider between posts");
        echo "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
            <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_divider\"class=\"mailpoet_automated_latest_content_show_divider\" value=\"true\" {{#if model.showDivider}}CHECKED{{/if}}/>
                    ";
        // line 313
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        echo "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_divider\"class=\"mailpoet_automated_latest_content_show_divider\" value=\"false\" {{#unless model.showDivider}}CHECKED{{/unless}}/>
                    ";
        // line 319
        echo $this->extensions['MailPoet\Twig\I18n']->translate("No");
        echo "
                </label>
            </div>
            <div>
                <a href=\"javascript:;\" class=\"mailpoet_automated_latest_content_select_divider\">";
        // line 323
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Select divider");
        echo "</a>
            </div>
        </div>

    </div>
</div>

<div class=\"mailpoet_form_field\">
    <input type=\"button\" data-automation-id=\"alc_settings_done\" class=\"button button-primary mailpoet_done_editing\" value=\"";
        // line 331
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Done"), "html_attr");
        echo "\" />
</div>

";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/automatedLatestContentLayout/settings.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  539 => 331,  528 => 323,  521 => 319,  512 => 313,  505 => 309,  495 => 302,  486 => 296,  479 => 292,  468 => 284,  456 => 275,  447 => 269,  440 => 265,  428 => 256,  422 => 253,  413 => 247,  404 => 241,  397 => 237,  387 => 230,  381 => 227,  372 => 221,  363 => 215,  356 => 211,  345 => 203,  336 => 197,  329 => 193,  319 => 186,  310 => 180,  301 => 174,  292 => 168,  283 => 162,  276 => 158,  265 => 150,  256 => 144,  249 => 140,  238 => 132,  229 => 126,  222 => 122,  213 => 116,  204 => 110,  195 => 104,  188 => 100,  179 => 94,  170 => 88,  161 => 82,  152 => 76,  145 => 72,  136 => 66,  127 => 60,  118 => 54,  106 => 45,  101 => 43,  87 => 32,  78 => 26,  59 => 10,  55 => 9,  51 => 8,  44 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/automatedLatestContentLayout/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/automatedLatestContentLayout/settings.hbs");
    }
}
