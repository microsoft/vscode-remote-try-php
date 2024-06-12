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

/* newsletter/templates/blocks/products/settingsSelection.hbs */
class __TwigTemplate_dfdf9f5a92a1c8ac3b2b3e97633af09605e9bf2c8350c290d8806af7dbad4bbd extends Template
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
        echo "<div class=\"mailpoet_settings_products_selection_controls\">
    <div class=\"mailpoet_product_selection_filter_row\">
        <input type=\"text\" name=\"\" class=\"mailpoet_input mailpoet_input_full mailpoet_products_search_term\" value=\"{{model.search}}\" placeholder=\"";
        // line 3
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Search...");
        echo "\" />
    </div>
    <div class=\"mailpoet_product_selection_filter_row\">
        <select class=\"mailpoet_select mailpoet_input_full mailpoet_products_post_status\">
            <option value=\"publish\" {{#ifCond model.postStatus '==' 'publish'}}SELECTED{{/ifCond}}>";
        // line 7
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Published");
        echo "</option>
            <option value=\"pending\" {{#ifCond model.postStatus '==' 'pending'}}SELECTED{{/ifCond}}>";
        // line 8
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Pending Review");
        echo "</option>
            <option value=\"draft\" {{#ifCond model.postStatus '==' 'draft'}}SELECTED{{/ifCond}}>";
        // line 9
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Draft");
        echo "</option>
        </select>
    </div>
    <div class=\"mailpoet_product_selection_filter_row\">
        <select class=\"mailpoet_select mailpoet_products_categories_and_tags\" multiple=\"multiple\">
          {{#each model.terms}}
            <option value=\"{{ id }}\" selected=\"selected\">{{ text }}</option>
          {{/each}}
        </select>
    </div>
</div>
<div class=\"mailpoet_product_selection_container\">
</div>
<div class=\"mailpoet_product_selection_loading\" style=\"visibility: hidden;\">
  ";
        // line 23
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Loading posts...");
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/products/settingsSelection.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 23,  56 => 9,  52 => 8,  48 => 7,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/products/settingsSelection.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/products/settingsSelection.hbs");
    }
}
