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

/* newsletter/templates/blocks/posts/settingsDisplayOptions.hbs */
class __TwigTemplate_cd01f7185dd0d7e17c724aeebb70949dd844805e176c8702c46453d27d2fb6fb extends Template
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
        yield "<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_display_type\" class=\"mailpoet_posts_display_type\" value=\"excerpt\" {{#ifCond model.displayType '==' 'excerpt'}}CHECKED{{/ifCond}}/>
            ";
        // line 5
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Excerpt");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_display_type\" class=\"mailpoet_posts_display_type\" value=\"full\" {{#ifCond model.displayType '==' 'full'}}CHECKED{{/ifCond}}/>
            ";
        // line 11
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Full post");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_display_type\" class=\"mailpoet_posts_display_type\" value=\"titleOnly\" {{#ifCond model.displayType '==' 'titleOnly'}}CHECKED{{/ifCond}} />
            ";
        // line 17
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title only");
        yield "
        </label>
    </div>
</div>

<hr class=\"mailpoet_separator\" />

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 25
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title Format");
        yield "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_format\" class=\"mailpoet_posts_title_format\" value=\"h1\" {{#ifCond model.titleFormat '==' 'h1'}}CHECKED{{/ifCond}}/>
            ";
        // line 29
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 1");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_format\" class=\"mailpoet_posts_title_format\" value=\"h2\" {{#ifCond model.titleFormat '==' 'h2'}}CHECKED{{/ifCond}}/>
            ";
        // line 35
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 2");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_format\" class=\"mailpoet_posts_title_format\" value=\"h3\" {{#ifCond model.titleFormat '==' 'h3'}}CHECKED{{/ifCond}}/>
            ";
        // line 41
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 3");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option mailpoet_posts_title_as_list {{#ifCond model.displayType '!=' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_format\" class=\"mailpoet_posts_title_format\" value=\"ul\" {{#ifCond model.titleFormat '==' 'ul'}}CHECKED{{/ifCond}}/>
            ";
        // line 47
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show as list");
        yield "
        </label>
    </div>
</div>

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 53
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title Alignment");
        yield "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_alignment\" class=\"mailpoet_posts_title_alignment\" value=\"left\" {{#ifCond model.titleAlignment '==' 'left'}}CHECKED{{/ifCond}} />
            ";
        // line 57
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_alignment\" class=\"mailpoet_posts_title_alignment\" value=\"center\" {{#ifCond model.titleAlignment '==' 'center'}}CHECKED{{/ifCond}} />
            ";
        // line 63
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Center");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_alignment\" class=\"mailpoet_posts_title_alignment\" value=\"right\" {{#ifCond model.titleAlignment '==' 'right'}}CHECKED{{/ifCond}} />
            ";
        // line 69
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        yield "
        </label>
    </div>
</div>

<div class=\"mailpoet_form_field mailpoet_posts_title_as_link {{#ifCond model.titleFormat '===' 'ul'}}mailpoet_hidden{{/ifCond}}\">
    <div class=\"mailpoet_form_field_title\">";
        // line 75
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Make the post title into a link");
        yield "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_as_links\" class=\"mailpoet_posts_title_as_links\" value=\"true\" {{#if model.titleIsLink}}CHECKED{{/if}}/>
            ";
        // line 79
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_as_links\" class=\"mailpoet_posts_title_as_links\" value=\"false\" {{#unless model.titleIsLink}}CHECKED{{/unless}}/>
            ";
        // line 85
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
        </label>
    </div>
</div>

<hr class=\"mailpoet_separator mailpoet_automated_latest_content_title_position_separator {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\" />

<div class=\"mailpoet_form_field mailpoet_automated_latest_content_title_position {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
    <div class=\"mailpoet_form_field_title\">";
        // line 93
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Title position", "Setting in the email designer to position the blog post title");
        yield "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_position\" class=\"mailpoet_automated_latest_content_title_position\" value=\"abovePost\" {{#ifCond model.titlePosition '!=' 'aboveExcerpt'}}CHECKED{{/ifCond}}/>
            ";
        // line 97
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Above the post", "Display the post title above the post block");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_position\" class=\"mailpoet_automated_latest_content_title_position\" value=\"aboveExcerpt\" {{#ifCond model.titlePosition '==' 'aboveExcerpt'}}CHECKED{{/ifCond}}/>
            ";
        // line 103
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Above the excerpt text", "Display the post title above the post excerpt");
        yield "
        </label>
    </div>
</div>

<hr class=\"mailpoet_separator mailpoet_posts_image_separator {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\" />

<div class=\"mailpoet_posts_non_title_list_options {{#ifCond model.displayType '==' 'titleOnly'}}{{#ifCond model.titleFormat '==' 'ul'}}mailpoet_hidden{{/ifCond}}{{/ifCond}}\">

    <div class=\"mailpoet_form_field mailpoet_posts_featured_image_position_container {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 113
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Featured image position");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_featured_image_position\" class=\"mailpoet_posts_featured_image_position\" value=\"centered\" {{#ifCond _featuredImagePosition '==' 'centered' }}CHECKED{{/ifCond}}/>
                ";
        // line 117
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Centered");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_featured_image_position\" class=\"mailpoet_posts_featured_image_position\" value=\"left\" {{#ifCond _featuredImagePosition '==' 'left' }}CHECKED{{/ifCond}}/>
                ";
        // line 123
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_featured_image_position\" class=\"mailpoet_posts_featured_image_position\" value=\"right\" {{#ifCond _featuredImagePosition '==' 'right' }}CHECKED{{/ifCond}}/>
                ";
        // line 129
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_featured_image_position\" class=\"mailpoet_posts_featured_image_position\" value=\"alternate\" {{#ifCond _featuredImagePosition '==' 'alternate' }}CHECKED{{/ifCond}}/>
                ";
        // line 135
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Alternate");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_featured_image_position\" class=\"mailpoet_posts_featured_image_position\" value=\"none\" {{#ifCond _featuredImagePosition '==' 'none' }}CHECKED{{/ifCond}}/>
                ";
        // line 141
        yield $this->extensions['MailPoet\Twig\I18n']->translate("None");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field mailpoet_posts_image_full_width_option {{#ifCond model.displayType '==' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 147
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Image width");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_posts_image_full_width\" value=\"true\" {{#if model.imageFullWidth}}CHECKED{{/if}}/>
                ";
        // line 151
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Full width");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_posts_image_full_width\" value=\"false\" {{#unless model.imageFullWidth}}CHECKED{{/unless}}/>
                ";
        // line 157
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Padded");
        yield "
            </label>
        </div>
    </div>

    <hr class=\"mailpoet_separator\" />

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title\">";
        // line 165
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show author");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_author\" class=\"mailpoet_posts_show_author\" value=\"no\" {{#ifCond model.showAuthor '==' 'no'}}CHECKED{{/ifCond}}/>
                ";
        // line 169
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_author\" class=\"mailpoet_posts_show_author\" value=\"aboveText\" {{#ifCond model.showAuthor '==' 'aboveText'}}CHECKED{{/ifCond}}/>
                ";
        // line 175
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Above text");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_author\" class=\"mailpoet_posts_show_author\" value=\"belowText\" {{#ifCond model.showAuthor '==' 'belowText'}}CHECKED{{/ifCond}}/>
                ";
        // line 181
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Below text");
        yield "<br />
            </label>
        </div>
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 184
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Preceded by:");
        yield "</div>
        <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
            <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_posts_author_preceded_by\" value=\"{{ model.authorPrecededBy }}\" />
        </div>
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title\">";
        // line 191
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show categories");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_categories\" class=\"mailpoet_posts_show_categories\" value=\"no\" {{#ifCond model.showCategories '==' 'no'}}CHECKED{{/ifCond}}/>
                ";
        // line 195
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_categories\" class=\"mailpoet_posts_show_categories\" value=\"aboveText\" {{#ifCond model.showCategories '==' 'aboveText'}}CHECKED{{/ifCond}}/>
                ";
        // line 201
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Above text");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_categories\" class=\"mailpoet_posts_show_categories\" value=\"belowText\" {{#ifCond model.showCategories '==' 'belowText'}}CHECKED{{/ifCond}}/>
                ";
        // line 207
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Below text");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 210
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Preceded by:");
        yield "</div>
        <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
            <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_posts_categories\" value=\"{{ model.categoriesPrecededBy }}\" />
        </div>
    </div>

    <hr class=\"mailpoet_separator\" />

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 219
        yield $this->extensions['MailPoet\Twig\I18n']->translate("\"Read more\" text");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_read_more_type\" class=\"mailpoet_posts_read_more_type\" value=\"link\" {{#ifCond model.readMoreType '==' 'link'}}CHECKED{{/ifCond}}/>
                ";
        // line 223
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Link");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_read_more_type\" class=\"mailpoet_posts_read_more_type\" value=\"button\" {{#ifCond model.readMoreType '==' 'button'}}CHECKED{{/ifCond}}/>
                ";
        // line 229
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Button");
        yield "
            </label>
        </div>

        <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
            <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_posts_read_more_text {{#ifCond model.readMoreType '!=' 'link'}}mailpoet_hidden{{/ifCond}}\" value=\"{{ model.readMoreText }}\" />
        </div>

        <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
            <a href=\"javascript:;\" class=\"mailpoet_posts_select_button {{#ifCond model.readMoreType '!=' 'button'}}mailpoet_hidden{{/ifCond}}\">";
        // line 238
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Design a button");
        yield "</a>
        </div>
    </div>

    <hr class=\"mailpoet_separator\" />
</div>

<div class=\"mailpoet_posts_non_title_list_options {{#ifCond model.displayType '==' 'titleOnly'}}{{#ifCond model.titleFormat '==' 'ul'}}mailpoet_hidden{{/ifCond}}{{/ifCond}}\">
    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small mailpoet_form_field_title_inline\">";
        // line 247
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show divider between posts");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_divider\" class=\"mailpoet_posts_show_divider\" value=\"true\" {{#if model.showDivider}}CHECKED{{/if}}/>
                ";
        // line 251
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_divider\"class=\"mailpoet_posts_show_divider\" value=\"false\" {{#unless model.showDivider}}CHECKED{{/unless}}/>
                ";
        // line 257
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_input_option\">
            <a href=\"javascript:;\" class=\"mailpoet_posts_select_divider\">";
        // line 261
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Select divider");
        yield "</a>
        </div>
    </div>
</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/blocks/posts/settingsDisplayOptions.hbs";
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
        return array (  432 => 261,  425 => 257,  416 => 251,  409 => 247,  397 => 238,  385 => 229,  376 => 223,  369 => 219,  357 => 210,  351 => 207,  342 => 201,  333 => 195,  326 => 191,  316 => 184,  310 => 181,  301 => 175,  292 => 169,  285 => 165,  274 => 157,  265 => 151,  258 => 147,  249 => 141,  240 => 135,  231 => 129,  222 => 123,  213 => 117,  206 => 113,  193 => 103,  184 => 97,  177 => 93,  166 => 85,  157 => 79,  150 => 75,  141 => 69,  132 => 63,  123 => 57,  116 => 53,  107 => 47,  98 => 41,  89 => 35,  80 => 29,  73 => 25,  62 => 17,  53 => 11,  44 => 5,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/posts/settingsDisplayOptions.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/posts/settingsDisplayOptions.hbs");
    }
}
