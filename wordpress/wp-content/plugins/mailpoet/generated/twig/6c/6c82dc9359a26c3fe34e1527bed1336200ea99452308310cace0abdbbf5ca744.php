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

/* newsletter/templates/blocks/coupon/settings.hbs */
class __TwigTemplate_242c7d80a2f2d73a801daf666a04cd25b175c9bb43d4ac8139c307f90cb229e5 extends Template
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
        yield "<h3 data-automation-id=\"coupon_settings_heading\">";
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Coupon");
        yield "</h3>

<div id=\"mailpoet_coupon_block_settings\" class=\"mailpoet_coupon_block_settings\"></div>

<div class=\"mailpoet_coupon_block_display_options mailpoet_closed\">
  <div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 7
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Alignment");
        yield "</div>
    <div class=\"mailpoet_form_field_radio_option\">
      <label>
        <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_coupon_alignment\" value=\"left\" {{#ifCond model.styles.block.textAlign '===' 'left'}}CHECKED{{/ifCond}}/>
        ";
        // line 11
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        yield "
      </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
      <label>
        <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_coupon_alignment\" value=\"center\" {{#ifCond model.styles.block.textAlign '===' 'center'}}CHECKED{{/ifCond}}/>
        ";
        // line 17
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Center");
        yield "
      </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
      <label>
        <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_coupon_alignment\" value=\"right\" {{#ifCond model.styles.block.textAlign '===' 'right'}}CHECKED{{/ifCond}}/>
        ";
        // line 23
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        yield "
      </label>
    </div>
  </div>
  <div class=\"mailpoet_form_field mailpoet_form_narrow_select2\">
    <div class=\"mailpoet_form_field_title\">";
        // line 28
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Text");
        yield "</div>
    <div class=\"mailpoet_form_field_input_option mailpoet_form_field_input_nowrap\">
      <input type=\"text\" name=\"font-color\" id=\"mailpoet_field_coupon_font_color\" class=\"mailpoet_field_coupon_font_color mailpoet_color\" value=\"{{ model.styles.block.fontColor }}\" />
      <select id=\"mailpoet_field_coupon_font_family\" name=\"font-family\" class=\"mailpoet_select mailpoet_select_medium mailpoet_field_coupon_font_family mailpoet_font_family\">
        <optgroup label=\"";
        // line 32
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Standard fonts");
        yield "\">
          {{#each availableStyles.fonts.standard}}
            <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.block.fontFamily}}SELECTED{{/ifCond}}>{{ this }}</option>
          {{/each}}
        </optgroup>
        <optgroup label=\"";
        // line 37
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Custom fonts");
        yield "\">
          {{#each availableStyles.fonts.custom}}
            <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.block.fontFamily}}SELECTED{{/ifCond}}>{{ this }}</option>
          {{/each}}
        </optgroup>
      </select>
      <select id=\"mailpoet_field_coupon_font_size\" name=\"font-size\" class=\"mailpoet_select mailpoet_select_small mailpoet_field_coupon_font_size mailpoet_font_size\">
        {{#each availableStyles.headingSizes}}
          <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.block.fontSize}}SELECTED{{/ifCond}}>{{ this }}</option>
        {{/each}}
      </select>
    </div>
  </div>
  <div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_checkbox_option\">
      <label>
        <input type=\"checkbox\" name=\"fontWeight\" class=\"mailpoet_field_coupon_font_weight\" value=\"bold\" {{#ifCond model.styles.block.fontWeight '===' 'bold'}}CHECKED{{/ifCond}}/>
        ";
        // line 54
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Bold");
        yield "
      </label>
    </div>
  </div>
  <div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_input_option\">
      <input type=\"text\" name=\"background-color\" class=\"mailpoet_field_coupon_background_color mailpoet_color\" value=\"{{ model.styles.block.backgroundColor }}\" />
    </div>
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 62
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Background");
        yield "</div>
  </div>
  <div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_input_option\">
      <input type=\"text\" name=\"border-color\" class=\"mailpoet_field_coupon_border_color mailpoet_color\" value=\"{{ model.styles.block.borderColor }}\" />
    </div>
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 68
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Border");
        yield "</div>
    <div class=\"mailpoet_form_field_input_option\">
      <input type=\"number\" name=\"border-width-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_coupon_border_width_input\" value=\"{{getNumber model.styles.block.borderWidth}}\" min=\"0\" max=\"10\" step=\"1\" /> px
      <input type=\"range\" min=\"0\" max=\"10\" step=\"1\" name=\"border-width\" class=\"mailpoet_range mailpoet_range_small mailpoet_field_coupon_border_width\" value=\"{{getNumber model.styles.block.borderWidth}}\" />
    </div>
  </div>
  <div class=\"mailpoet_form_field\">
    <label>
      <div class=\"mailpoet_form_field_title\">";
        // line 76
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Rounded corners");
        yield "</div>
      <div class=\"mailpoet_form_field_input_option\">
        <input type=\"number\" name=\"border-radius-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_coupon_border_radius_input\" value=\"{{getNumber model.styles.block.borderRadius}}\" min=\"0\" max=\"40\" step=\"1\" /> px
        <input type=\"range\" min=\"0\" max=\"40\" step=\"1\" name=\"border-radius\" class=\"mailpoet_range mailpoet_range_medium mailpoet_field_coupon_border_radius\" value=\"{{getNumber model.styles.block.borderRadius }}\" />
      </div>
    </label>
  </div>
  <div class=\"mailpoet_form_field\">
    <label>
      <div class=\"mailpoet_form_field_title\">";
        // line 85
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Width");
        yield "</div>
      <div class=\"mailpoet_form_field_input_option\">
        <input type=\"number\" name=\"width-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_coupon_width_input\" value=\"{{getNumber model.styles.block.width}}\" min=\"50\" max=\"288\" step=\"1\" /> px
        <input type=\"range\" min=\"50\" max=\"288\" step=\"1\" name=\"width\" class=\"mailpoet_range mailpoet_range_medium mailpoet_field_coupon_width\" value=\"{{getNumber model.styles.block.width }}\" />
      </div>
    </label>
  </div>
  <div class=\"mailpoet_form_field\">
    <label>
      <div class=\"mailpoet_form_field_title\">";
        // line 94
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Height");
        yield "</div>
      <div class=\"mailpoet_form_field_input_option\">
        <input type=\"number\" name=\"line-height-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_coupon_line_height_input\" value=\"{{getNumber model.styles.block.lineHeight}}\" min=\"20\" max=\"50\" step=\"1\" /> px
        <input type=\"range\" min=\"20\" max=\"50\" step=\"1\" name=\"line-height\" class=\"mailpoet_range mailpoet_range_medium mailpoet_field_coupon_line_height\" value=\"{{getNumber model.styles.block.lineHeight }}\" />
      </div>
    </label>
  </div>
  {{#ifCond renderOptions.hideApplyToAll '!==' true}}
  <div class=\"mailpoet_form_field\">
    <input type=\"button\" name=\"replace-all-coupon-styles\" class=\"button button-secondary mailpoet_coupon_full mailpoet_field_coupon_replace_all_styles\" value=\"";
        // line 103
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("Apply styles to all coupons"), "html_attr");
        yield "\" />
  </div>
  {{/ifCond}}
</div>

<div class=\"mailpoet_form_field\">
    <a href=\"javascript:;\" class=\"mailpoet_settings_coupon_show_coupon_configuration mailpoet_hidden\">";
        // line 109
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Back to selection");
        yield "</a>
    <a href=\"javascript:;\" class=\"mailpoet_settings_coupon_show_display_options\">";
        // line 110
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Display options");
        yield "</a>
</div>

<div class=\"mailpoet_form_field\">
    <input type=\"button\" class=\"button button-primary mailpoet_done_editing\" data-automation-id=\"coupon_done_button\" value=\"";
        // line 114
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("Done"), "html_attr");
        yield "\" />
</div>

<script type=\"text/javascript\">
    fontsSelect('#mailpoet_field_coupon_font_family');
</script>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/blocks/coupon/settings.hbs";
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
        return array (  203 => 114,  196 => 110,  192 => 109,  183 => 103,  171 => 94,  159 => 85,  147 => 76,  136 => 68,  127 => 62,  116 => 54,  96 => 37,  88 => 32,  81 => 28,  73 => 23,  64 => 17,  55 => 11,  48 => 7,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/coupon/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/coupon/settings.hbs");
    }
}
