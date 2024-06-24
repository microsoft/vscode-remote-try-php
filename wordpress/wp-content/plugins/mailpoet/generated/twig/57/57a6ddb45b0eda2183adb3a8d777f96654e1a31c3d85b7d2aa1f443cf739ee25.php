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
        yield "<div class=\"mailpoet_settings_posts_selection_controls\">
    <div class=\"mailpoet_post_selection_filter_row\">
        <input type=\"text\" name=\"\" class=\"mailpoet_input mailpoet_input_full mailpoet_posts_search_term\" value=\"{{model.search}}\" placeholder=\"";
        // line 3
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Search...");
        yield "\" />
    </div>
    <div class=\"mailpoet_post_selection_filter_row\"><select name=\"mailpoet_posts_content_type\" class=\"mailpoet_select mailpoet_select_half_width mailpoet_settings_posts_content_type\">
            <option value=\"post\" {{#ifCond model.contentType '==' 'post'}}SELECTED{{/ifCond}}>";
        // line 6
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Posts");
        yield "</option>
            <option value=\"page\" {{#ifCond model.contentType '==' 'page'}}SELECTED{{/ifCond}}>";
        // line 7
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Pages");
        yield "</option>
            <option value=\"mailpoet_page\" {{#ifCond model.contentType '==' 'mailpoet_page'}}SELECTED{{/ifCond}}>";
        // line 8
        yield $this->extensions['MailPoet\Twig\I18n']->translate("MailPoet pages");
        yield "</option>
        </select><select class=\"mailpoet_select mailpoet_select_half_width mailpoet_posts_post_status\">
            <option value=\"publish\" {{#ifCond model.postStatus '==' 'publish'}}SELECTED{{/ifCond}}>";
        // line 10
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Published");
        yield "</option>
            <option value=\"future\" {{#ifCond model.postStatus '==' 'future'}}SELECTED{{/ifCond}}>";
        // line 11
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Scheduled");
        yield "</option>
            <option value=\"draft\" {{#ifCond model.postStatus '==' 'draft'}}SELECTED{{/ifCond}}>";
        // line 12
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Draft");
        yield "</option>
            <option value=\"pending\" {{#ifCond model.postStatus '==' 'pending'}}SELECTED{{/ifCond}}>";
        // line 13
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Pending Review");
        yield "</option>
            <option value=\"private\" {{#ifCond model.postStatus '==' 'private'}}SELECTED{{/ifCond}}>";
        // line 14
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Private");
        yield "</option>
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
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Loading posts...");
        yield "
</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/blocks/posts/settingsSelection.hbs";
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
        return array (  93 => 27,  77 => 14,  73 => 13,  69 => 12,  65 => 11,  61 => 10,  56 => 8,  52 => 7,  48 => 6,  42 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/posts/settingsSelection.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/posts/settingsSelection.hbs");
    }
}
