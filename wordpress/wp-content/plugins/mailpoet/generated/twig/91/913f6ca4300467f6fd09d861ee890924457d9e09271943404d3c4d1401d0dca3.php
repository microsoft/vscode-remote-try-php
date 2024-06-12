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

/* newsletter/templates/blocks/social/settingsIcon.hbs */
class __TwigTemplate_a6bc3737d02c4217a15108cd3f8addf2e33ea8546e47c75fe38c1b5ca5b7d106 extends Template
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
        echo "<div class=\"mailpoet_social_icon_settings\">
    <div class=\"mailpoet_social_icon_settings_tool mailpoet_social_icon_settings_move_icon\">
        <a href=\"javascript:;\" class=\"mailpoet_move_block\">";
        // line 3
        echo \MailPoetVendor\twig_source($this->env, "newsletter/templates/svg/block-tools/move-without-bg.svg");
        echo "</a>
    </div>
    <div class=\"mailpoet_social_icon_settings_tool mailpoet_social_icon_settings_delete_icon\">
        <a href=\"javascript:;\" class=\"mailpoet_delete_block\">";
        // line 6
        echo \MailPoetVendor\twig_source($this->env, "newsletter/templates/svg/block-tools/trash-without-bg.svg");
        echo "</a>
    </div>
    <div class=\"mailpoet_social_icon_settings_row\">
        <label>
        <div class=\"mailpoet_social_icon_settings_label mailpoet_social_icon_image_label\">
            <img src=\"{{ model.image }}\" onerror=\"if (this.src != '{{ allIconSets.default.custom }}') this.src = '{{ allIconSets.default.custom }}';\" alt=\"{{ model.text }}\" class=\"mailpoet_social_icon_image\" />
        </div>
        <div class=\"mailpoet_social_icon_settings_form_element\">
            <select name=\"iconType\" class=\"mailpoet_social_icon_field_type\">
            {{#each iconTypes}}
                <option value=\"{{ iconType }}\" {{#ifCond iconType '==' ../model.iconType}}SELECTED{{/ifCond}}>{{ title }}</option>
            {{/each}}
            </select>
        </div>
        </label>
    </div>

    {{#ifCond iconType '==' 'custom'}}
    <div class=\"mailpoet_social_icon_settings_row\">
        <label>
        <div class=\"mailpoet_social_icon_settings_label\">
            ";
        // line 27
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Image");
        echo "
        </div>
        <div class=\"mailpoet_social_icon_settings_form_element\">
            <input type=\"text\" name=\"image\" class=\"mailpoet_social_icon_field_image\" value=\"{{ model.image }}\" placeholder=\"http://\" />
        </div>
        </label>
    </div>
    {{/ifCond}}

    <div class=\"mailpoet_social_icon_settings_row\">
        <label>
        <div class=\"mailpoet_social_icon_settings_label\">
            {{ currentType.linkFieldName }}
        </div>
        <div class=\"mailpoet_social_icon_settings_form_element\">
            {{#ifCond iconType '==' 'email'}}
            <input type=\"text\" name=\"link\" class=\"mailpoet_social_icon_field_link\" value=\"{{emailFromMailto model.link }}\" placeholder=\"example@example.org\" /><br />
            {{else}}
            <input type=\"text\" name=\"link\" class=\"mailpoet_social_icon_field_link\" value=\"{{ model.link }}\" placeholder=\"http://\" /><br />
            {{/ifCond}}
        </div>
        </label>
    </div>

    {{#ifCond iconType '==' 'custom'}}
    <div class=\"mailpoet_social_icon_settings_row\">
        <label>
        <div class=\"mailpoet_social_icon_settings_label\">
            ";
        // line 55
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Text");
        echo "
        </div>
        <div class=\"mailpoet_social_icon_settings_form_element\">
            <input type=\"text\" name=\"text\" class=\"mailpoet_social_icon_field_text\" value=\"{{ model.text }}\" />
        </div>
        </label>
    </div>
    {{/ifCond}}
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/social/settingsIcon.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  102 => 55,  71 => 27,  47 => 6,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/social/settingsIcon.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/social/settingsIcon.hbs");
    }
}
