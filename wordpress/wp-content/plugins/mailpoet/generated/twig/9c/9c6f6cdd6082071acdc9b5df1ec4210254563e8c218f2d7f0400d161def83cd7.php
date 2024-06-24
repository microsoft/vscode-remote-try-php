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

/* newsletter/templates/blocks/automatedLatestContent/settings.hbs */
class __TwigTemplate_ee165e5b4f8563d8513127bab042c7a2b7959b76320c0c8e57a68effec71bd92 extends Template
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
        <input type=\"text\" class=\"mailpoet_input mailpoet_input_small mailpoet_automated_latest_content_show_amount\" value=\"{{ model.amount }}\" maxlength=\"2\" size=\"2\" />
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
    <a href=\"javascript:;\" class=\"mailpoet_automated_latest_content_show_display_options\">";
        // line 41
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Display options");
        yield "</a>
</div>
<div class=\"mailpoet_automated_latest_content_display_options mailpoet_closed\">
    <div class=\"mailpoet_form_field\">
        <a href=\"javascript:;\" class=\"mailpoet_automated_latest_content_hide_display_options\">";
        // line 45
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Hide display options");
        yield "</a>
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_display_type\" class=\"mailpoet_automated_latest_content_display_type\" value=\"excerpt\" {{#ifCond model.displayType '==' 'excerpt'}}CHECKED{{/ifCond}}/>
                ";
        // line 52
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Excerpt");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_display_type\" class=\"mailpoet_automated_latest_content_display_type\" value=\"full\" {{#ifCond model.displayType '==' 'full'}}CHECKED{{/ifCond}}/>
                ";
        // line 58
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Full post");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_display_type\" class=\"mailpoet_automated_latest_content_display_type\" value=\"titleOnly\" {{#ifCond model.displayType '==' 'titleOnly'}}CHECKED{{/ifCond}} />
                ";
        // line 64
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title only");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title\">";
        // line 70
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title Format");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"h1\" {{#ifCond model.titleFormat '==' 'h1'}}CHECKED{{/ifCond}}/>
                ";
        // line 74
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 1");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"h2\" {{#ifCond model.titleFormat '==' 'h2'}}CHECKED{{/ifCond}}/>
                ";
        // line 80
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 2");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"h3\" {{#ifCond model.titleFormat '==' 'h3'}}CHECKED{{/ifCond}}/>
                ";
        // line 86
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 3");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option mailpoet_automated_latest_content_title_as_list {{#ifCond model.displayType '!=' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_format\" class=\"mailpoet_automated_latest_content_title_format\" value=\"ul\" {{#ifCond model.titleFormat '==' 'ul'}}CHECKED{{/ifCond}}/>
                ";
        // line 92
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show as list");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title\">";
        // line 98
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title Alignment");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_alignment\" class=\"mailpoet_automated_latest_content_title_alignment\" value=\"left\" {{#ifCond model.titleAlignment '==' 'left'}}CHECKED{{/ifCond}} />
                ";
        // line 102
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_alignment\" class=\"mailpoet_automated_latest_content_title_alignment\" value=\"center\" {{#ifCond model.titleAlignment '==' 'center'}}CHECKED{{/ifCond}} />
                ";
        // line 108
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Center");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_alignment\" class=\"mailpoet_automated_latest_content_title_alignment\" value=\"right\" {{#ifCond model.titleAlignment '==' 'right'}}CHECKED{{/ifCond}} />
                ";
        // line 114
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field mailpoet_automated_latest_content_title_as_link {{#ifCond model.titleFormat '===' 'ul'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 120
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title as links");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_as_links\" class=\"mailpoet_automated_latest_content_title_as_links\" value=\"true\" {{#if model.titleIsLink}}CHECKED{{/if}}/>
                ";
        // line 124
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_as_links\" class=\"mailpoet_automated_latest_content_title_as_links\" value=\"false\" {{#unless model.titleIsLink}}CHECKED{{/unless}}/>
                ";
        // line 130
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
            </label>
        </div>
    </div>

    <hr class=\"mailpoet_separator mailpoet_automated_latest_content_image_separator {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\" />

    <div class=\"mailpoet_form_field mailpoet_automated_latest_content_featured_image_position_container {{#ifCond model.displayType '!==' 'excerpt'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 138
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Featured image position");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"belowTitle\" {{#ifCond model.featuredImagePosition '==' 'belowTitle' }}CHECKED{{/ifCond}}/>
                ";
        // line 142
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Below title");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"aboveTitle\" {{#ifCond model.featuredImagePosition '==' 'aboveTitle' }}CHECKED{{/ifCond}}/>
                ";
        // line 148
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Above title");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_featured_image_position\" class=\"mailpoet_automated_latest_content_featured_image_position\" value=\"none\" {{#ifCond model.featuredImagePosition '==' 'none' }}CHECKED{{/ifCond}}/>
                ";
        // line 154
        yield $this->extensions['MailPoet\Twig\I18n']->translate("None");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_automated_latest_content_non_title_list_options {{#ifCond model.displayType '==' 'titleOnly'}}{{#ifCond model.titleFormat '==' 'ul'}}mailpoet_hidden{{/ifCond}}{{/ifCond}}\">
        <div class=\"mailpoet_form_field mailpoet_automated_latest_content_image_full_width_option {{#ifCond model.displayType '==' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
            <div class=\"mailpoet_form_field_title\">";
        // line 161
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Image width");
        yield "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_automated_latest_content_image_full_width\" value=\"true\" {{#if model.imageFullWidth}}CHECKED{{/if}}/>
                    ";
        // line 165
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Full width");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_automated_latest_content_image_full_width\" value=\"false\" {{#unless model.imageFullWidth}}CHECKED{{/unless}}/>
                    ";
        // line 171
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Padded");
        yield "
                </label>
            </div>
        </div>

        <hr class=\"mailpoet_separator\" />

        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title\">";
        // line 179
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show author");
        yield "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_author\" class=\"mailpoet_automated_latest_content_show_author\" value=\"no\" {{#ifCond model.showAuthor '==' 'no'}}CHECKED{{/ifCond}}/>
                    ";
        // line 183
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_author\" class=\"mailpoet_automated_latest_content_show_author\" value=\"aboveText\" {{#ifCond model.showAuthor '==' 'aboveText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 189
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Above text");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_author\" class=\"mailpoet_automated_latest_content_show_author\" value=\"belowText\" {{#ifCond model.showAuthor '==' 'belowText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 195
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Below text");
        yield "<br />
                </label>
            </div>
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 198
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Preceded by:");
        yield "</div>
            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_automated_latest_content_author_preceded_by\" value=\"{{ model.authorPrecededBy }}\" />
            </div>
        </div>

        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title\">";
        // line 205
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show categories");
        yield "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_categories\" class=\"mailpoet_automated_latest_content_show_categories\" value=\"no\" {{#ifCond model.showCategories '==' 'no'}}CHECKED{{/ifCond}}/>
                    ";
        // line 209
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_categories\" class=\"mailpoet_automated_latest_content_show_categories\" value=\"aboveText\" {{#ifCond model.showCategories '==' 'aboveText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 215
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Above text");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_categories\" class=\"mailpoet_automated_latest_content_show_categories\" value=\"belowText\" {{#ifCond model.showCategories '==' 'belowText'}}CHECKED{{/ifCond}}/>
                    ";
        // line 221
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Below text");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 224
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Preceded by:");
        yield "</div>
            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_automated_latest_content_categories\" value=\"{{ model.categoriesPrecededBy }}\" />
            </div>
        </div>

        <hr class=\"mailpoet_separator\" />

        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 233
        yield $this->extensions['MailPoet\Twig\I18n']->translate("\"Read more\" text");
        yield "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_read_more_type\" class=\"mailpoet_automated_latest_content_read_more_type\" value=\"link\" {{#ifCond model.readMoreType '==' 'link'}}CHECKED{{/ifCond}}/>
                    ";
        // line 237
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Link");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_read_more_type\" class=\"mailpoet_automated_latest_content_read_more_type\" value=\"button\" {{#ifCond model.readMoreType '==' 'button'}}CHECKED{{/ifCond}}/>
                    ";
        // line 243
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Button");
        yield "
                </label>
            </div>

            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_automated_latest_content_read_more_text {{#ifCond model.readMoreType '!=' 'link'}}mailpoet_hidden{{/ifCond}}\" value=\"{{ model.readMoreText }}\" />
            </div>

            <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
                <a href=\"javascript:;\" class=\"mailpoet_automated_latest_content_select_button {{#ifCond model.readMoreType '!=' 'button'}}mailpoet_hidden{{/ifCond}}\">";
        // line 252
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Design a button");
        yield "</a>
            </div>
        </div>

        <hr class=\"mailpoet_separator\" />
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 260
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Sort by");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_sort_by\" class=\"mailpoet_automated_latest_content_sort_by\" value=\"newest\" {{#ifCond model.sortBy '==' 'newest'}}CHECKED{{/ifCond}}/>
                ";
        // line 264
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Newest");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_automated_latest_content_sort_by\" class=\"mailpoet_automated_latest_content_sort_by\" value=\"oldest\" {{#ifCond model.sortBy '==' 'oldest'}}CHECKED{{/ifCond}}/>
                ";
        // line 270
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Oldest");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_automated_latest_content_non_title_list_options {{#ifCond model.displayType '==' 'titleOnly'}}{{#ifCond model.titleFormat '==' 'ul'}}mailpoet_hidden{{/ifCond}}{{/ifCond}}\">
        <div class=\"mailpoet_form_field\">
            <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 277
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show divider between posts");
        yield "</div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
            <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_divider\"class=\"mailpoet_automated_latest_content_show_divider\" value=\"true\" {{#if model.showDivider}}CHECKED{{/if}}/>
                    ";
        // line 281
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        yield "
                </label>
            </div>
            <div class=\"mailpoet_form_field_radio_option\">
                <label>
                    <input type=\"radio\" name=\"mailpoet_automated_latest_content_show_divider\"class=\"mailpoet_automated_latest_content_show_divider\" value=\"false\" {{#unless model.showDivider}}CHECKED{{/unless}}/>
                    ";
        // line 287
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
                </label>
            </div>
            <div>
                <a href=\"javascript:;\" class=\"mailpoet_automated_latest_content_select_divider\">";
        // line 291
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Select divider");
        yield "</a>
            </div>
        </div>

    </div>
</div>

<div class=\"mailpoet_form_field\">
    <input type=\"button\" class=\"button button-primary mailpoet_done_editing\" value=\"";
        // line 299
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
        return "newsletter/templates/blocks/automatedLatestContent/settings.hbs";
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
        return array (  493 => 299,  482 => 291,  475 => 287,  466 => 281,  459 => 277,  449 => 270,  440 => 264,  433 => 260,  422 => 252,  410 => 243,  401 => 237,  394 => 233,  382 => 224,  376 => 221,  367 => 215,  358 => 209,  351 => 205,  341 => 198,  335 => 195,  326 => 189,  317 => 183,  310 => 179,  299 => 171,  290 => 165,  283 => 161,  273 => 154,  264 => 148,  255 => 142,  248 => 138,  237 => 130,  228 => 124,  221 => 120,  212 => 114,  203 => 108,  194 => 102,  187 => 98,  178 => 92,  169 => 86,  160 => 80,  151 => 74,  144 => 70,  135 => 64,  126 => 58,  117 => 52,  107 => 45,  100 => 41,  88 => 32,  79 => 26,  60 => 10,  56 => 9,  52 => 8,  45 => 4,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/automatedLatestContent/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/automatedLatestContent/settings.hbs");
    }
}
