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

/* newsletter/templates/blocks/posts/settingsSelection.hbs */
class __TwigTemplate_02deb84769839d0a6212d921e633c1fa1d1daa6b34177426293099b2197e4112 extends Template
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
        echo "<div class=\"mailpoet_settings_posts_selection_controls\">
    <div class=\"mailpoet_post_selection_filter_row\">
        <input type=\"text\" name=\"\" class=\"mailpoet_input mailpoet_input_full mailpoet_posts_search_term\" value=\"{{model.search}}\" placeholder=\"";
        // line 3
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Search...");
        echo "\" />
    </div>
    <div class=\"mailpoet_post_selection_filter_row\"><select name=\"mailpoet_posts_content_type\" class=\"mailpoet_select mailpoet_select_half_width mailpoet_settings_posts_content_type\">
            <option value=\"post\" {{#ifCond model.contentType '==' 'post'}}SELECTED{{/ifCond}}>";
        // line 6
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Posts");
        echo "</option>
            <option value=\"page\" {{#ifCond model.contentType '==' 'page'}}SELECTED{{/ifCond}}>";
        // line 7
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Pages");
        echo "</option>
            <option value=\"mailpoet_page\" {{#ifCond model.contentType '==' 'mailpoet_page'}}SELECTED{{/ifCond}}>";
        // line 8
        echo $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet pages");
        echo "</option>
        </select><select class=\"mailpoet_select mailpoet_select_half_width mailpoet_posts_post_status\">
            <option value=\"publish\" {{#ifCond model.postStatus '==' 'publish'}}SELECTED{{/ifCond}}>";
        // line 10
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Published");
        echo "</option>
            <option value=\"future\" {{#ifCond model.postStatus '==' 'future'}}SELECTED{{/ifCond}}>";
        // line 11
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Scheduled");
        echo "</option>
            <option value=\"draft\" {{#ifCond model.postStatus '==' 'draft'}}SELECTED{{/ifCond}}>";
        // line 12
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Draft");
        echo "</option>
            <option value=\"pending\" {{#ifCond model.postStatus '==' 'pending'}}SELECTED{{/ifCond}}>";
        // line 13
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Pending Review");
        echo "</option>
            <option value=\"private\" {{#ifCond model.postStatus '==' 'private'}}SELECTED{{/ifCond}}>";
        // line 14
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Private");
        echo "</option>
        </select></div>
    <div class=\"mailpoet_post_selection_filter_row\">
        <select class=\"mailpoet_select mailpoet_posts_categories_and_tags\" multiple=\"multiple\">
          {{#each model.terms}}
            <option value=\"{{ id }}\" selected=\"selected\">{{ text }}</option>
          {{/each}}
        </select>
    </div>
</div>
<div class=\"mailpoet_post_selection_container\">
</div>
<div class=\"mailpoet_post_selection_loading\" style=\"visibility: hidden;\">
  ";
        // line 27
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Loading posts...");
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/posts/settingsSelection.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 27,  76 => 14,  72 => 13,  68 => 12,  64 => 11,  60 => 10,  55 => 8,  51 => 7,  47 => 6,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/posts/settingsSelection.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/posts/settingsSelection.hbs");
    }
}
