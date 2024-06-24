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

/* newsletter/templates/blocks/base/toolsGeneric.hbs */
class __TwigTemplate_f0b7c0a4cecb3b89ab65459add490fc50c30c23e24fcb855983e41c79ddf8b4b extends Template
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
        yield "<div class=\"mailpoet_tools_slider\">
\t{{#if tools.layerSelector}}
\t\t<a href=\"javascript:;\" class=\"mailpoet_tool mailpoet_newsletter_layer_selector mailpoet_ignore_drag\" title=\"";
        // line 3
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Switch editing layer");
        yield "\">
    \t\t";
        // line 4
        yield MailPoetVendor\Twig\Extension\CoreExtension::source($this->env, "newsletter/templates/svg/block-tools/settings.svg");
        yield "
\t\t</a>
\t{{/if}}
\t{{#if tools.settings}}
\t\t<a href=\"javascript:;\" data-automation-id=\"settings_tool\" class=\"mailpoet_tool mailpoet_edit_block mailpoet_ignore_drag\" title=\"";
        // line 8
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Edit settings");
        yield "\">
\t\t\t";
        // line 9
        yield MailPoetVendor\Twig\Extension\CoreExtension::source($this->env, "newsletter/templates/svg/block-tools/settings.svg");
        yield "
\t\t</a>
\t{{/if}}
\t{{#if tools.delete}}
\t\t<div class=\"mailpoet_delete_block mailpoet_ignore_drag\">
\t\t\t<a href=\"javascript:;\" class=\"mailpoet_tool mailpoet_delete_block_activate\" title=\"";
        // line 14
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Delete");
        yield "\">
\t\t\t\t";
        // line 15
        yield MailPoetVendor\Twig\Extension\CoreExtension::source($this->env, "newsletter/templates/svg/block-tools/trash.svg");
        yield "
\t\t\t</a>
\t\t\t<a href=\"javascript:;\" class=\"mailpoet_delete_block_cancel\" title=\"";
        // line 17
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Cancel deletion");
        yield "\">
\t\t\t\t";
        // line 18
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Cancel");
        yield "
\t\t\t</a>
\t\t\t<a href=\"javascript:;\" class=\"mailpoet_delete_block_confirm\" title=\"";
        // line 20
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Confirm deletion");
        yield "\">
\t\t\t\t";
        // line 21
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Delete");
        yield "
\t\t\t</a>
\t\t</div>
\t{{/if}}
\t{{#if tools.duplicate}}
\t\t<a href=\"javascript:;\" data-automation-id=\"duplicate_tool\" class=\"mailpoet_tool mailpoet_duplicate_block\" title=\"";
        // line 26
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Duplicate");
        yield "\">
\t\t\t";
        // line 27
        yield MailPoetVendor\Twig\Extension\CoreExtension::source($this->env, "newsletter/templates/svg/block-tools/duplicate.svg");
        yield "
\t\t</a>
\t{{/if}}
\t{{#if tools.move}}
\t\t<a href=\"javascript:;\" class=\"mailpoet_tool mailpoet_move_block\" title=\"";
        // line 31
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Drag to move");
        yield "\">
  \t\t\t";
        // line 32
        yield MailPoetVendor\Twig\Extension\CoreExtension::source($this->env, "newsletter/templates/svg/block-tools/move.svg");
        yield "
\t\t</a>
\t{{/if}}
</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/blocks/base/toolsGeneric.hbs";
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
        return array (  110 => 32,  106 => 31,  99 => 27,  95 => 26,  87 => 21,  83 => 20,  78 => 18,  74 => 17,  69 => 15,  65 => 14,  57 => 9,  53 => 8,  46 => 4,  42 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/base/toolsGeneric.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/base/toolsGeneric.hbs");
    }
}
