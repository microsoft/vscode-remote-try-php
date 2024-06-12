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

/* newsletter/templates/blocks/container/settings.hbs */
class __TwigTemplate_97885955d9a61cd687e4b5c17a9f999c888cf1faf1a92c3e61951df96457e75b extends Template
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
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Columns");
        echo "</h3>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"text\" name=\"background-color\" class=\"mailpoet_field_container_background_color mailpoet_color\" value=\"{{ model.styles.block.backgroundColor }}\" />
        </div>
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 7
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Background color");
        echo "</div>
    </label>
</div>
<hr>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title\">";
        // line 13
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Background image");
        echo "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"text\" name=\"src\" class=\"mailpoet_input mailpoet_field_image_address\" value=\"{{ model.image.src }}\" placeholder=\"http://\" /><br />
        </div>
    </label>
</div>
<div class=\"mailpoet_form_field\">
    <input type=\"button\" name=\"select-image\" class=\"button button-secondary mailpoet_button_full mailpoet_field_image_select_image\" value=\"{{#if model.image.src}}";
        // line 20
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Select another image"), "html_attr");
        echo "{{else}}";
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Select background image", "call to action to select an image as a background image of a layout block"), "html_attr");
        echo "{{/if}}\" />
</div>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 23
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Display options");
        echo "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"display_type\" class=\"mailpoet_field_display_type\" value=\"scale\" {{#ifCond model.image.display '===' 'scale'}}CHECKED{{/ifCond}}/>
            ";
        // line 27
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Scale");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"display_type\" class=\"mailpoet_field_display_type\" value=\"fit\" {{#ifCond model.image.display '===' 'fit'}}CHECKED{{/ifCond}}/>
            ";
        // line 33
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Fit");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"display_type\" class=\"mailpoet_field_display_type\" value=\"tile\" {{#ifCond model.image.display '===' 'tile'}}CHECKED{{/ifCond}}/>
            ";
        // line 39
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Tile");
        echo "
        </label>
    </div>
</div>
<div class=\"mailpoet_form_field\">
  <p class=\"mailpoet_settings_notice\">";
        // line 44
        echo MailPoet\Util\Helpers::replaceLinkTags($this->extensions['MailPoet\Twig\I18n']->translateWithContext("A [link]few email clients[/link] do not show background images. Make sure you have a good background color to fall back on if the image is not displayed.", "Editor > right sidebar for column block settings."), "https://kb.mailpoet.com/article/261-add-a-background-image-to-your-newsletter", ["target" => "_blank"]);
        echo "</p>
</div>
<div class=\"mailpoet_form_field\">
    <input type=\"button\" class=\"button button-primary mailpoet_done_editing\" value=\"";
        // line 47
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Done"), "html_attr");
        echo "\" />
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/container/settings.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  113 => 47,  107 => 44,  99 => 39,  90 => 33,  81 => 27,  74 => 23,  66 => 20,  56 => 13,  47 => 7,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/container/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/container/settings.hbs");
    }
}
