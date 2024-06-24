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

/* newsletter/templates/blocks/abandonedCartContent/settingsDisplayOptions.hbs */
class __TwigTemplate_06eb69614e63782dbf212c517de98466ef764932657f95db41872addc4edfe3f extends Template
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
            <input type=\"radio\" name=\"mailpoet_products_display_type\" class=\"mailpoet_products_display_type\" value=\"titleOnly\" {{#ifCond model.displayType '==' 'titleOnly'}}CHECKED{{/ifCond}}/>
            ";
        // line 5
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title only");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_products_display_type\" class=\"mailpoet_products_display_type\" value=\"excerpt\" {{#ifCond model.displayType '==' 'excerpt'}}CHECKED{{/ifCond}}/>
            ";
        // line 11
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title and a short description");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_products_display_type\" class=\"mailpoet_products_display_type\" value=\"full\" {{#ifCond model.displayType '==' 'full'}}CHECKED{{/ifCond}} />
            ";
        // line 17
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title and description");
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
            <input type=\"radio\" name=\"mailpoet_products_title_format\" class=\"mailpoet_products_title_format\" value=\"h1\" {{#ifCond model.titleFormat '==' 'h1'}}CHECKED{{/ifCond}}/>
            ";
        // line 29
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 1");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_products_title_format\" class=\"mailpoet_products_title_format\" value=\"h2\" {{#ifCond model.titleFormat '==' 'h2'}}CHECKED{{/ifCond}}/>
            ";
        // line 35
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 2");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_products_title_format\" class=\"mailpoet_products_title_format\" value=\"h3\" {{#ifCond model.titleFormat '==' 'h3'}}CHECKED{{/ifCond}}/>
            ";
        // line 41
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Heading 3");
        yield "
        </label>
    </div>
</div>

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 47
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Title Alignment");
        yield "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_products_title_alignment\" class=\"mailpoet_products_title_alignment\" value=\"left\" {{#ifCond model.titleAlignment '==' 'left'}}CHECKED{{/ifCond}} />
            ";
        // line 51
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_products_title_alignment\" class=\"mailpoet_products_title_alignment\" value=\"center\" {{#ifCond model.titleAlignment '==' 'center'}}CHECKED{{/ifCond}} />
            ";
        // line 57
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Center");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_products_title_alignment\" class=\"mailpoet_products_title_alignment\" value=\"right\" {{#ifCond model.titleAlignment '==' 'right'}}CHECKED{{/ifCond}} />
            ";
        // line 63
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        yield "
        </label>
    </div>
</div>

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 69
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Make the product title into a link", "Display the product title into a link");
        yield "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_products_title_as_links\" class=\"mailpoet_products_title_as_links\" value=\"true\" {{#if model.titleIsLink}}CHECKED{{/if}}/>
            ";
        // line 73
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_products_title_as_links\" class=\"mailpoet_products_title_as_links\" value=\"false\" {{#unless model.titleIsLink}}CHECKED{{/unless}}/>
            ";
        // line 79
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
        </label>
    </div>
</div>

<hr class=\"mailpoet_separator\" />

<div class=\"mailpoet_form_field mailpoet_products_title_position {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
    <div class=\"mailpoet_form_field_title\">";
        // line 87
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Product title position", "Setting in the email designer to position an ecommerce product title");
        yield "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_products_title_position\" class=\"mailpoet_products_title_position\" value=\"abovePost\" {{#ifCond model.titlePosition '!=' 'aboveExcerpt'}}CHECKED{{/ifCond}}/>
            ";
        // line 91
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Above the product", "Display the product title above the product block");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"mailpoet_products_title_position\" class=\"mailpoet_products_title_position\" value=\"aboveExcerpt\" {{#ifCond model.titlePosition '==' 'aboveExcerpt'}}CHECKED{{/ifCond}}/>
            ";
        // line 97
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Above the product description", "Display the product title above the product description");
        yield "
        </label>
    </div>
</div>

<hr class=\"mailpoet_separator mailpoet_products_title_position_separator {{#ifCond model.displayType '===' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\" />

<div> <!-- empty div for better git diff -->
    <div class=\"mailpoet_form_field mailpoet_products_featured_image_position_container\">
        <div class=\"mailpoet_form_field_title\">";
        // line 106
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Product image position");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_products_featured_image_position\" class=\"mailpoet_products_featured_image_position\" value=\"centered\" {{#ifCond model.featuredImagePosition '==' 'centered' }}CHECKED{{/ifCond}}/>
                ";
        // line 110
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Centered");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_products_featured_image_position\" class=\"mailpoet_products_featured_image_position\" value=\"left\" {{#ifCond model.featuredImagePosition '==' 'left' }}CHECKED{{/ifCond}}/>
                ";
        // line 116
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_products_featured_image_position\" class=\"mailpoet_products_featured_image_position\" value=\"right\" {{#ifCond model.featuredImagePosition '==' 'right' }}CHECKED{{/ifCond}}/>
                ";
        // line 122
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_products_featured_image_position\" class=\"mailpoet_products_featured_image_position\" value=\"alternate\" {{#ifCond model.featuredImagePosition '==' 'alternate' }}CHECKED{{/ifCond}}/>
                ";
        // line 128
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Alternate");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_products_featured_image_position\" class=\"mailpoet_products_featured_image_position\" value=\"none\" {{#ifCond model.featuredImagePosition '==' 'none' }}CHECKED{{/ifCond}}/>
                ";
        // line 134
        yield $this->extensions['MailPoet\Twig\I18n']->translate("None");
        yield "
            </label>
        </div>
    </div>

    <div class=\"mailpoet_form_field mailpoet_products_image_full_width_option {{#ifCond model.displayType '==' 'titleOnly'}}mailpoet_hidden{{/ifCond}}\">
        <div class=\"mailpoet_form_field_title\">";
        // line 140
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Image width");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_products_image_full_width\" value=\"true\" {{#if model.imageFullWidth}}CHECKED{{/if}}/>
                ";
        // line 144
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Full width");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"imageFullWidth\" class=\"mailpoet_products_image_full_width\" value=\"false\" {{#unless model.imageFullWidth}}CHECKED{{/unless}}/>
                ";
        // line 150
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Padded");
        yield "
            </label>
        </div>
    </div>

    <hr class=\"mailpoet_separator\" />

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title\">";
        // line 158
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Price");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_products_price_position\" class=\"mailpoet_products_price_position\" value=\"below\" {{#ifCond model.pricePosition '==' 'below'}}CHECKED{{/ifCond}} />
                ";
        // line 162
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Below text");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_products_price_position\" class=\"mailpoet_products_price_position\" value=\"above\" {{#ifCond model.pricePosition '==' 'above'}}CHECKED{{/ifCond}} />
                ";
        // line 168
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Above text");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_products_price_position\" class=\"mailpoet_products_price_position\" value=\"hidden\" {{#ifCond model.pricePosition '==' 'hidden'}}CHECKED{{/ifCond}} />
                ";
        // line 174
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
            </label>
        </div>
    </div>

    <hr class=\"mailpoet_separator\" />

    <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_small mailpoet_form_field_title_inline\">";
        // line 182
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Show divider between products");
        yield "</div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_products_show_divider\" class=\"mailpoet_products_show_divider\" value=\"true\" {{#if model.showDivider}}CHECKED{{/if}}/>
                ";
        // line 186
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_radio_option\">
            <label>
                <input type=\"radio\" name=\"mailpoet_products_show_divider\"class=\"mailpoet_products_show_divider\" value=\"false\" {{#unless model.showDivider}}CHECKED{{/unless}}/>
                ";
        // line 192
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
            </label>
        </div>
        <div class=\"mailpoet_form_field_input_option\">
            <a href=\"javascript:;\" class=\"mailpoet_products_select_divider\">";
        // line 196
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
        return "newsletter/templates/blocks/abandonedCartContent/settingsDisplayOptions.hbs";
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
        return array (  334 => 196,  327 => 192,  318 => 186,  311 => 182,  300 => 174,  291 => 168,  282 => 162,  275 => 158,  264 => 150,  255 => 144,  248 => 140,  239 => 134,  230 => 128,  221 => 122,  212 => 116,  203 => 110,  196 => 106,  184 => 97,  175 => 91,  168 => 87,  157 => 79,  148 => 73,  141 => 69,  132 => 63,  123 => 57,  114 => 51,  107 => 47,  98 => 41,  89 => 35,  80 => 29,  73 => 25,  62 => 17,  53 => 11,  44 => 5,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/abandonedCartContent/settingsDisplayOptions.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/abandonedCartContent/settingsDisplayOptions.hbs");
    }
}
