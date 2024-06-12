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

/* newsletter/templates/blocks/image/settings.hbs */
class __TwigTemplate_22a77faa62e190779c8dce6c27d815339e592767581cf8e9672f667844f6939d extends Template
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
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Image");
        echo "<span id=\"tooltip-designer-ideal-width\" class=\"tooltip-help-designer-ideal-width\"></span></h3>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title\">";
        // line 4
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Link");
        echo " <span class=\"mailpoet_form_field_optional\">(";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Optional");
        echo ")</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"text\" name=\"src\" class=\"mailpoet_input mailpoet_field_image_link\" value=\"{{ model.link }}\" placeholder=\"http://\" />
        </div>
    </label>
</div>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title\">";
        // line 12
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Image address", "input field for the image URL");
        echo "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"text\" name=\"src\" class=\"mailpoet_input mailpoet_field_image_address\" value=\"{{ model.src }}\" placeholder=\"http://\" /><br />
        </div>
    </label>
</div>
<div class=\"mailpoet_form_field\">
    <input type=\"button\" name=\"select-image\" class=\"button button-secondary mailpoet_button_full mailpoet_field_image_select_image\" value=\"";
        // line 19
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Select another image"), "html_attr");
        echo "\" />
</div>

<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title\">";
        // line 24
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Alternative text");
        echo "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"text\" name=\"alt\" class=\"mailpoet_input mailpoet_field_image_alt_text\" value=\"{{ model.alt }}\" />
        </div>
    </label>
</div>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title\">";
        // line 32
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Width");
        echo "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input
                class=\"mailpoet_input mailpoet_input_small mailpoet_field_image_width_input\"
                name=\"image-width-input\"
                type=\"number\"
                value=\"{{getNumber model.width}}\"
                min=\"36\"
                max=\"660\"
                step=\"2\"
            /> px
            <input
                class=\"mailpoet_range mailpoet_range_small mailpoet_field_image_width\"
                name=\"image-width\"
                type=\"range\"
                value=\"{{getNumber model.width}}\"
                min=\"36\"
                max=\"660\"
                step=\"2\"
            />
        </div>
    </label>
</div>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_checkbox_option\">
        <label>
            <input type=\"checkbox\" name=\"fullWidth\" class=\"mailpoet_field_image_full_width\" value=\"true\" {{#if model.fullWidth }}CHECKED{{/if}}/>
            ";
        // line 59
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("No padding", "Option to remove a default padding around images");
        echo "
        </label>
        <span id=\"tooltip-designer-full-width\" class=\"tooltip-help-designer-full-width\"></span>
    </div>
</div>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 65
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Alignment");
        echo "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
        <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_image_alignment\" value=\"left\" {{#ifCond model.styles.block.textAlign '===' 'left'}}CHECKED{{/ifCond}}/>
        ";
        // line 69
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_image_alignment\" value=\"center\" {{#ifCond model.styles.block.textAlign '===' 'center'}}CHECKED{{/ifCond}}/>
            ";
        // line 75
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Center");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_image_alignment\" value=\"right\" {{#ifCond model.styles.block.textAlign '===' 'right'}}CHECKED{{/ifCond}}/>
            ";
        // line 81
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        echo "
        </label>
    </div>
</div>

<div class=\"mailpoet_form_field\">
    <input type=\"button\" class=\"button button-primary mailpoet_done_editing\" value=\"";
        // line 87
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Done"), "html_attr");
        echo "\" />
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/image/settings.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  159 => 87,  150 => 81,  141 => 75,  132 => 69,  125 => 65,  116 => 59,  86 => 32,  75 => 24,  67 => 19,  57 => 12,  44 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/image/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/image/settings.hbs");
    }
}
