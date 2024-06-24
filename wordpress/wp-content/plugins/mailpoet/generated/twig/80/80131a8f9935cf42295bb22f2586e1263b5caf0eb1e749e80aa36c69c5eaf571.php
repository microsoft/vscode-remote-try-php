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

/* newsletter/templates/blocks/divider/settings.hbs */
class __TwigTemplate_912232d8d98ec89992f5e6290c3740bfaa82c9b5be05df35d42defcaf1c4b665 extends Template
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
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Dividers");
        yield "</h3>
<div class=\"mailpoet_divider_selector\" data-automation-id=\"divider_selector\">
{{#each availableStyles.dividers}}
    <div class=\"mailpoet_field_divider_style{{#ifCond this '==' ../model.styles.block.borderStyle}} mailpoet_active_divider_style{{/ifCond}}\" data-style=\"{{ this }}\">
        <div style=\"border-top-width: 5px; border-top-style: {{ this }}; border-top-color: {{ ../model.styles.block.borderColor }};\"></div>
    </div>
{{/each}}
</div>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"number\" name=\"border-width-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_divider_border_width_input\" value=\"{{getNumber model.styles.block.borderWidth}}\" min=\"1\" max=\"30\" step=\"1\" /> px
            <input type=\"range\" min=\"1\" max=\"30\" step=\"1\" name=\"divider-width\" class=\"mailpoet_range mailpoet_range_small mailpoet_field_divider_border_width\" value=\"{{getNumber model.styles.block.borderWidth }}\" />
        </div>
        <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 15
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Divider height");
        yield "</div>
    </label>
</div>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"text\" name=\"divider-color\" class=\"mailpoet_field_divider_border_color mailpoet_color\" value=\"{{ model.styles.block.borderColor }}\" />
    </div>
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 22
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Divider color");
        yield "</div>
</div>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"text\" name=\"background-color\" class=\"mailpoet_field_divider_background_color mailpoet_color\" value=\"{{ model.styles.block.backgroundColor }}\" />
    </div>
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 28
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Background");
        yield "</div>
</div>
{{#ifCond renderOptions.hideApplyToAll '!==' true}}
<div class=\"mailpoet_form_field\">
    <input type=\"button\" name=\"apply-to-all-dividers\" class=\"button button-secondary mailpoet_button_full mailpoet_button_divider_apply_to_all\" value=\"";
        // line 32
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("Apply to all dividers"), "html_attr");
        yield "\" />
</div>
{{/ifCond}}

<div class=\"mailpoet_form_field\">
    <input type=\"button\" class=\"button button-primary mailpoet_done_editing\" value=\"";
        // line 37
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
        return "newsletter/templates/blocks/divider/settings.hbs";
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
        return array (  90 => 37,  82 => 32,  75 => 28,  66 => 22,  56 => 15,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/divider/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/divider/settings.hbs");
    }
}
