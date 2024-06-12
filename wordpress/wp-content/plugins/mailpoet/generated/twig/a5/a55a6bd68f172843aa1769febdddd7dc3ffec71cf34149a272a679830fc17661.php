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
        echo "<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_display_type\" class=\"mailpoet_posts_display_type\" value=\"excerpt\" {{#ifCond model.displayType '==' 'excerpt'}}CHECKED{{/ifCond}}/>
            ";
        // line 5
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Excerpt");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_display_type\" class=\"mailpoet_posts_display_type\" value=\"full\" {{#ifCond model.displayType '==' 'full'}}CHECKED{{/ifCond}}/>
            ";
        // line 11
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Full post");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_display_type\" class=\"mailpoet_posts_display_type\" value=\"titleOnly\" {{#ifCond model.displayType '==' 'titleOnly'}}CHECKED{{/ifCond}} />
            ";
        // line 17
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Title only");
        echo "
        </label>
    </div>
</div>

<hr class=\"mailpoet_separator\" />

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 25
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Title Format");
        echo "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_format\" class=\"mailpoet_posts_title_format\" value=\"h1\" {{#ifCond model.titleFormat '==' 'h1'}}CHECKED{{/ifCond}}/>
            ";
        // line 29
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Heading 1");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_format\" class=\"mailpoet_posts_title_format\" value=\"h2\" {{#ifCond model.titleFormat '==' 'h2'}}CHECKED{{/ifCond}}/>
            ";
        // line 35
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Heading 2");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_format\" class=\"mailpoet_posts_title_format\" value=\"h3\" {{#ifCond model.titleFormat '==' 'h3'}}CHECKED{{/ifCond}}/>
            ";
        // line 41
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Heading 3");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option mailpoet_posts_title_as_list {{#ifCond model.displayType '!=' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_format\" class=\"mailpoet_posts_title_format\" value=\"ul\" {{#ifCond model.titleFormat '==' 'ul'}}CHECKED{{/ifCond}}/>
            ";
        // line 47
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Show as list");
        echo "
        </label>
    </div>
</div>

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 53
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Title Alignment");
        echo "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_alignment\" class=\"mailpoet_posts_title_alignment\" value=\"left\" {{#ifCond model.titleAlignment '==' 'left'}}CHECKED{{/ifCond}} />
            ";
        // line 57
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_alignment\" class=\"mailpoet_posts_title_alignment\" value=\"center\" {{#ifCond model.titleAlignment '==' 'center'}}CHECKED{{/ifCond}} />
            ";
        // line 63
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Center");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_alignment\" class=\"mailpoet_posts_title_alignment\" value=\"right\" {{#ifCond model.titleAlignment '==' 'right'}}CHECKED{{/ifCond}} />
            ";
        // line 69
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        echo "
        </label>
    </div>
</div>

<div class=\"mailpoet_form_field mailpoet_posts_title_as_link {{#ifCond model.titleFormat '===' 'ul'}}mailpoet_hidden{{/ifCond}}\">
    <div class=\"mailpoet_form_field_title\">";
        // line 75
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Make the post title into a link");
        echo "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_as_links\" class=\"mailpoet_posts_title_as_links\" value=\"true\" {{#if model.titleIsLink}}CHECKED{{/if}}/>
            ";
        // line 79
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_posts_title_as_links\" class=\"mailpoet_posts_title_as_links\" value=\"false\" {{#unless model.titleIsLink}}CHECKED{{/unless}}/>
            ";
        // line 85
        echo $this->extensions['MailPoet\Twig\I18n']->translate("No");
        echo "
        </label>
    </div>
</div>

<hr class=\"mailpoet_separator mailpoet_automated_latest_content_title_position_separator {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\" />

<div class=\"mailpoet_form_field mailpoet_automated_latest_content_title_position {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
    <div class=\"mailpoet_form_field_title\">";
        // line 93
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Title position", "Setting in the email designer to position the blog post title");
        echo "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_position\" class=\"mailpoet_automated_latest_content_title_position\" value=\"abovePost\" {{#ifCond model.titlePosition '!=' 'aboveExcerpt'}}CHECKED{{/ifCond}}/>
            ";
        // line 97
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Above the post", "Display the post title above the post block");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_automated_latest_content_title_position\" class=\"mailpoet_automated_latest_content_title_position\" value=\"aboveExcerpt\" {{#ifCond model.titlePosition '==' 'aboveExcerpt'}}CHECKED{{/ifCond}}/>
            ";
        // line 103
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Above the excerpt text", "Display the post title above the post excerpt");
        echo "
        </label>
    </div>
</div>

<hr class=\"mailpoet_separator mailpoet_posts_image_separator {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\" />

<div class=\"mailpoet_posts_non_title_list_options {{#ifCond model.displayType '==' 'titleOnly'}}{{#ifCond model.titleFormat '==' 'ul'}}mailpoet_hidden{{/ifCond}}{{/ifCond}}\">

    <div class=\"mailpoet_form_field mailpoet_posts_featured_image_position_container {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 113
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Featured image position");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_featured_image_position\" class=\"mailpoet_posts_featured_image_position\" value=\"centered\" {{#ifCond _featuredImagePosition '==' 'centered' }}CHECKED{{/ifCond}}/>
                ";
        // line 117
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Centered");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_featured_image_position\" class=\"mailpoet_posts_featured_image_position\" value=\"left\" {{#ifCond _featuredImagePosition '==' 'left' }}CHECKED{{/ifCond}}/>
                ";
        // line 123
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_featured_image_position\" class=\"mailpoet_posts_featured_image_position\" value=\"right\" {{#ifCond _featuredImagePosition '==' 'right' }}CHECKED{{/ifCond}}/>
                ";
        // line 129
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_featured_image_position\" class=\"mailpoet_posts_featured_image_position\" value=\"alternate\" {{#ifCond _featuredImagePosition '==' 'alternate' }}CHECKED{{/ifCond}}/>
                ";
        // line 135
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Alternate");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_featured_image_position\" class=\"mailpoet_posts_featured_image_position\" value=\"none\" {{#ifCond _featuredImagePosition '==' 'none' }}CHECKED{{/ifCond}}/>
                ";
        // line 141
        echo $this->extensions['MailPoet\Twig\I18n']->translate("None");
        echo "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field mailpoet_posts_image_full_width_option {{#ifCond model.displayType '==' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 147
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Image width");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_posts_image_full_width\" value=\"true\" {{#if model.imageFullWidth}}CHECKED{{/if}}/>
                ";
        // line 151
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Full width");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_posts_image_full_width\" value=\"false\" {{#unless model.imageFullWidth}}CHECKED{{/unless}}/>
                ";
        // line 157
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Padded");
        echo "
            </label>
        </div>
    </div>

    <hr class=\"mailpoet_separator\" />

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title\">";
        // line 165
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Show author");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_author\" class=\"mailpoet_posts_show_author\" value=\"no\" {{#ifCond model.showAuthor '==' 'no'}}CHECKED{{/ifCond}}/>
                ";
        // line 169
        echo $this->extensions['MailPoet\Twig\I18n']->translate("No");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_author\" class=\"mailpoet_posts_show_author\" value=\"aboveText\" {{#ifCond model.showAuthor '==' 'aboveText'}}CHECKED{{/ifCond}}/>
                ";
        // line 175
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Above text");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_author\" class=\"mailpoet_posts_show_author\" value=\"belowText\" {{#ifCond model.showAuthor '==' 'belowText'}}CHECKED{{/ifCond}}/>
                ";
        // line 181
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Below text");
        echo "<br />
            </label>
        </div>
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 184
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Preceded by:");
        echo "</div>
        <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
            <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_posts_author_preceded_by\" value=\"{{ model.authorPrecededBy }}\" />
        </div>
    </div>

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title\">";
        // line 191
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Show categories");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_categories\" class=\"mailpoet_posts_show_categories\" value=\"no\" {{#ifCond model.showCategories '==' 'no'}}CHECKED{{/ifCond}}/>
                ";
        // line 195
        echo $this->extensions['MailPoet\Twig\I18n']->translate("No");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_categories\" class=\"mailpoet_posts_show_categories\" value=\"aboveText\" {{#ifCond model.showCategories '==' 'aboveText'}}CHECKED{{/ifCond}}/>
                ";
        // line 201
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Above text");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_categories\" class=\"mailpoet_posts_show_categories\" value=\"belowText\" {{#ifCond model.showCategories '==' 'belowText'}}CHECKED{{/ifCond}}/>
                ";
        // line 207
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Below text");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 210
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Preceded by:");
        echo "</div>
        <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
            <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_posts_categories\" value=\"{{ model.categoriesPrecededBy }}\" />
        </div>
    </div>

    <hr class=\"mailpoet_separator\" />

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small\">";
        // line 219
        echo $this->extensions['MailPoet\Twig\I18n']->translate("\"Read more\" text");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_read_more_type\" class=\"mailpoet_posts_read_more_type\" value=\"link\" {{#ifCond model.readMoreType '==' 'link'}}CHECKED{{/ifCond}}/>
                ";
        // line 223
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Link");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_read_more_type\" class=\"mailpoet_posts_read_more_type\" value=\"button\" {{#ifCond model.readMoreType '==' 'button'}}CHECKED{{/ifCond}}/>
                ";
        // line 229
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Button");
        echo "
            </label>
        </div>

        <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
            <input type=\"text\" class=\"mailpoet_input mailpoet_input_full mailpoet_posts_read_more_text {{#ifCond model.readMoreType '!=' 'link'}}mailpoet_hidden{{/ifCond}}\" value=\"{{ model.readMoreText }}\" />
        </div>

        <div class=\"mailpoet_form_field_input_option mailpoet_form_field_block\">
            <a href=\"javascript:;\" class=\"mailpoet_posts_select_button {{#ifCond model.readMoreType '!=' 'button'}}mailpoet_hidden{{/ifCond}}\">";
        // line 238
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Design a button");
        echo "</a>
        </div>
    </div>

    <hr class=\"mailpoet_separator\" />
</div>

<div class=\"mailpoet_posts_non_title_list_options {{#ifCond model.displayType '==' 'titleOnly'}}{{#ifCond model.titleFormat '==' 'ul'}}mailpoet_hidden{{/ifCond}}{{/ifCond}}\">
    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small mailpoet_form_field_title_inline\">";
        // line 247
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Show divider between posts");
        echo "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_divider\" class=\"mailpoet_posts_show_divider\" value=\"true\" {{#if model.showDivider}}CHECKED{{/if}}/>
                ";
        // line 251
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_posts_show_divider\"class=\"mailpoet_posts_show_divider\" value=\"false\" {{#unless model.showDivider}}CHECKED{{/unless}}/>
                ";
        // line 257
        echo $this->extensions['MailPoet\Twig\I18n']->translate("No");
        echo "
            </label>
        </div>
        <div class=\"mailpoet_form_field_input_option\">
            <a href=\"javascript:;\" class=\"mailpoet_posts_select_divider\">";
        // line 261
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Select divider");
        echo "</a>
        </div>
    </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/posts/settingsDisplayOptions.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  431 => 261,  424 => 257,  415 => 251,  408 => 247,  396 => 238,  384 => 229,  375 => 223,  368 => 219,  356 => 210,  350 => 207,  341 => 201,  332 => 195,  325 => 191,  315 => 184,  309 => 181,  300 => 175,  291 => 169,  284 => 165,  273 => 157,  264 => 151,  257 => 147,  248 => 141,  239 => 135,  230 => 129,  221 => 123,  212 => 117,  205 => 113,  192 => 103,  183 => 97,  176 => 93,  165 => 85,  156 => 79,  149 => 75,  140 => 69,  131 => 63,  122 => 57,  115 => 53,  106 => 47,  97 => 41,  88 => 35,  79 => 29,  72 => 25,  61 => 17,  52 => 11,  43 => 5,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/posts/settingsDisplayOptions.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/posts/settingsDisplayOptions.hbs");
    }
}
