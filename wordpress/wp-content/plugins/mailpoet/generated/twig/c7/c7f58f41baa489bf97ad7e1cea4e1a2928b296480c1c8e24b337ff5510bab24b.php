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

/* newsletter/templates/blocks/button/settings.hbs */
class __TwigTemplate_43903aab9ead5da2aa4fd5513357866620f7fd81d3e3f3af87287dcca1489303 extends Template
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
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Button");
        yield "</h3>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 4
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Label");
        yield "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"text\" name=\"text\" class=\"mailpoet_input mailpoet_field_button_text\" value=\"{{ model.text }}\" />
        </div>
    </label>
</div>

{{#ifCond renderOptions.hideLink '!==' true}}
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 14
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Link");
        yield "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"text\" name=\"url\" class=\"mailpoet_input mailpoet_field_button_url\" value=\"{{ model.url }}\" placeholder=\"http://\" />
        </div>
    </label>
</div>
{{/ifCond}}

<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 23
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Alignment");
        yield "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
        <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_button_alignment\" value=\"left\" {{#ifCond model.styles.block.textAlign '===' 'left'}}CHECKED{{/ifCond}}/>
        ";
        // line 27
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_button_alignment\" value=\"center\" {{#ifCond model.styles.block.textAlign '===' 'center'}}CHECKED{{/ifCond}}/>
            ";
        // line 33
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Center");
        yield "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_button_alignment\" value=\"right\" {{#ifCond model.styles.block.textAlign '===' 'right'}}CHECKED{{/ifCond}}/>
            ";
        // line 39
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        yield "
        </label>
    </div>
</div>
<div class=\"mailpoet_form_field mailpoet_form_narrow_select2\">
    <div class=\"mailpoet_form_field_title\">";
        // line 44
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Text");
        yield "</div>
    <div class=\"mailpoet_form_field_input_option mailpoet_form_field_input_nowrap\">
        <input type=\"text\" name=\"font-color\" id=\"mailpoet_field_button_font_color\" class=\"mailpoet_field_button_font_color mailpoet_color\" value=\"{{ model.styles.block.fontColor }}\" />
        <select id=\"mailpoet_field_button_font_family\" name=\"font-family\" class=\"mailpoet_select mailpoet_select_medium mailpoet_field_button_font_family mailpoet_font_family\">
        <optgroup label=\"";
        // line 48
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Standard fonts");
        yield "\">
        {{#each availableStyles.fonts.standard}}
            <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.block.fontFamily}}SELECTED{{/ifCond}}>{{ this }}</option>
        {{/each}}
        </optgroup>
        <optgroup label=\"";
        // line 53
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Custom fonts");
        yield "\">
        {{#each availableStyles.fonts.custom}}
            <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.block.fontFamily}}SELECTED{{/ifCond}}>{{ this }}</option>
        {{/each}}
        </optgroup>
        </select>
        <select id=\"mailpoet_field_button_font_size\" name=\"font-size\" class=\"mailpoet_select mailpoet_select_small mailpoet_field_button_font_size mailpoet_font_size\">
        {{#each availableStyles.headingSizes}}
            <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.block.fontSize}}SELECTED{{/ifCond}}>{{ this }}</option>
        {{/each}}
        </select>
    </div>
</div>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_checkbox_option\">
        <label>
            <input type=\"checkbox\" name=\"fontWeight\" class=\"mailpoet_field_button_font_weight\" value=\"bold\" {{#ifCond model.styles.block.fontWeight '===' 'bold'}}CHECKED{{/ifCond}}/>
            ";
        // line 70
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Bold");
        yield "
        </label>
    </div>
</div>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"text\" name=\"background-color\" class=\"mailpoet_field_button_background_color mailpoet_color\" value=\"{{ model.styles.block.backgroundColor }}\" />
    </div>
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 78
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Background");
        yield "</div>
</div>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"text\" name=\"border-color\" class=\"mailpoet_field_button_border_color mailpoet_color\" value=\"{{ model.styles.block.borderColor }}\" />
    </div>
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 84
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Border");
        yield "</div>
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"number\" name=\"border-width-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_button_border_width_input\" value=\"{{getNumber model.styles.block.borderWidth}}\" min=\"0\" max=\"10\" step=\"1\" /> px
        <input type=\"range\" min=\"0\" max=\"10\" step=\"1\" name=\"border-width\" class=\"mailpoet_range mailpoet_range_small mailpoet_field_button_border_width\" value=\"{{getNumber model.styles.block.borderWidth}}\" />
    </div>
</div>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title\">";
        // line 92
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Rounded corners");
        yield "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"number\" name=\"border-radius-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_button_border_radius_input\" value=\"{{getNumber model.styles.block.borderRadius}}\" min=\"0\" max=\"40\" step=\"1\" /> px
            <input type=\"range\" min=\"0\" max=\"40\" step=\"1\" name=\"border-radius\" class=\"mailpoet_range mailpoet_range_medium mailpoet_field_button_border_radius\" value=\"{{getNumber model.styles.block.borderRadius }}\" />
        </div>
    </label>
</div>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title\">";
        // line 101
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Width");
        yield "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"number\" name=\"width-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_button_width_input\" value=\"{{getNumber model.styles.block.width}}\" min=\"50\" max=\"288\" step=\"1\" /> px
            <input type=\"range\" min=\"50\" max=\"288\" step=\"1\" name=\"width\" class=\"mailpoet_range mailpoet_range_medium mailpoet_field_button_width\" value=\"{{getNumber model.styles.block.width }}\" />
        </div>
    </label>
</div>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title\">";
        // line 110
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Height");
        yield "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"number\" name=\"line-height-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_button_line_height_input\" value=\"{{getNumber model.styles.block.lineHeight}}\" min=\"20\" max=\"50\" step=\"1\" /> px
            <input type=\"range\" min=\"20\" max=\"50\" step=\"1\" name=\"line-height\" class=\"mailpoet_range mailpoet_range_medium mailpoet_field_button_line_height\" value=\"{{getNumber model.styles.block.lineHeight }}\" />
        </div>
    </label>
</div>
{{#ifCond renderOptions.hideApplyToAll '!==' true}}
<div class=\"mailpoet_form_field\">
    <input type=\"button\" name=\"replace-all-button-styles\" class=\"button button-secondary mailpoet_button_full mailpoet_field_button_replace_all_styles\" value=\"";
        // line 119
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("Apply styles to all buttons"), "html_attr");
        yield "\" />
</div>
{{/ifCond}}

<div class=\"mailpoet_form_field\">
    <input type=\"button\" class=\"button button-primary mailpoet_done_editing\" value=\"";
        // line 124
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("Done"), "html_attr");
        yield "\" />
</div>

<script type=\"text/javascript\">
    fontsSelect('#mailpoet_field_button_font_family');
</script>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/blocks/button/settings.hbs";
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
        return array (  213 => 124,  205 => 119,  193 => 110,  181 => 101,  169 => 92,  158 => 84,  149 => 78,  138 => 70,  118 => 53,  110 => 48,  103 => 44,  95 => 39,  86 => 33,  77 => 27,  70 => 23,  58 => 14,  45 => 4,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/button/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/button/settings.hbs");
    }
}
