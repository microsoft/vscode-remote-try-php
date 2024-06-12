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

/* newsletter/templates/blocks/header/settings.hbs */
class __TwigTemplate_6600fff5a3f9873439474a07dab72aaab7a4466f14e9ab17e0fb500adeecf368 extends Template
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
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Header");
        echo "</h3>
<div class=\"mailpoet_form_field mailpoet_form_narrow_select2\">
    <div class=\"mailpoet_form_field_title\">";
        // line 3
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Text");
        echo "</div>
    <div class=\"mailpoet_form_field_input_option mailpoet_form_field_input_nowrap\">
        <input type=\"text\" name=\"font-color\" id=\"mailpoet_field_header_text_color\" class=\"mailpoet_field_header_text_color mailpoet_color\" value=\"{{ model.styles.text.fontColor }}\" />
        <select id=\"mailpoet_field_header_text_font_family\" name=\"font-family\" class=\"mailpoet_select mailpoet_select_medium mailpoet_field_header_text_font_family mailpoet_font_family\">

        <optgroup label=\"";
        // line 8
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Standard fonts");
        echo "\">
        {{#each availableStyles.fonts.standard}}
            <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.text.fontFamily}}SELECTED{{/ifCond}}>{{ this }}</option>
        {{/each}}
        </optgroup>
        <optgroup label=\"";
        // line 13
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Custom fonts");
        echo "\">
        {{#each availableStyles.fonts.custom}}
            <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.text.fontFamily}}SELECTED{{/ifCond}}>{{ this }}</option>
        {{/each}}
        </optgroup>
        </select>
        <select id=\"mailpoet_field_header_text_size\" name=\"font-size\" class=\"mailpoet_select mailpoet_select_small mailpoet_field_header_text_size mailpoet_font_size\">
        {{#each availableStyles.textSizes}}
            <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.text.fontSize}}SELECTED{{/ifCond}}>{{ this }}</option>
        {{/each}}
        </select>
    </div>
</div>
<div class=\"mailpoet_form_field\">
      <div class=\"mailpoet_form_field_input_option\">
          <input type=\"text\" class=\"mailpoet_color\" size=\"6\" maxlength=\"6\" name=\"link-color\" value=\"{{ model.styles.link.fontColor }}\" id=\"mailpoet_field_header_link_color\" />
      </div>
      <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 30
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Links");
        echo "</div>
    <label>
        <div class=\"mailpoet_form_field_checkbox_option mailpoet_option_offset_left_small\">
            <input type=\"checkbox\" name=\"underline\" value=\"underline\" id=\"mailpoet_field_header_link_underline\" {{#ifCond model.styles.link.textDecoration '==' 'underline'}}CHECKED{{/ifCond}}/> ";
        // line 33
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Underline");
        echo "
        </div>
    </label>
</div>

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"text\" name=\"background-color\" class=\"mailpoet_field_header_background_color mailpoet_color\" value=\"{{ model.styles.block.backgroundColor }}\" />
    </div>
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 42
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Background");
        echo "</div>
</div>

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
        <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_header_alignment\" value=\"left\" {{#ifCond model.styles.text.textAlign '===' 'left'}}CHECKED{{/ifCond}}/>
        ";
        // line 49
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_header_alignment\" value=\"center\" {{#ifCond model.styles.text.textAlign '===' 'center'}}CHECKED{{/ifCond}}/>
            ";
        // line 55
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Center");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_header_alignment\" value=\"right\" {{#ifCond model.styles.text.textAlign '===' 'right'}}CHECKED{{/ifCond}}/>
            ";
        // line 61
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        echo "
        </label>
    </div>
</div>

<div class=\"mailpoet_form_field\">
    <input type=\"button\" class=\"button button-primary mailpoet_done_editing\" data-automation-id=\"header_done_button\" value=\"";
        // line 67
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Done"), "html_attr");
        echo "\" />
</div>

<p class=\"mailpoet_settings_notice\">";
        // line 70
        echo MailPoet\Util\Helpers::replaceLinkTags($this->extensions['MailPoet\Twig\I18n']->translate("If an email client [link]does not support a custom web font[/link], a similar standard font will be used instead."), "https://kb.mailpoet.com/article/176-which-fonts-can-be-used-in-mailpoet#custom-web-fonts", ["target" => "_blank"]);
        echo "</p>

<script type=\"text/javascript\">
    fontsSelect('#mailpoet_field_header_text_font_family');
</script>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/header/settings.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  140 => 70,  134 => 67,  125 => 61,  116 => 55,  107 => 49,  97 => 42,  85 => 33,  79 => 30,  59 => 13,  51 => 8,  43 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/header/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/header/settings.hbs");
    }
}
