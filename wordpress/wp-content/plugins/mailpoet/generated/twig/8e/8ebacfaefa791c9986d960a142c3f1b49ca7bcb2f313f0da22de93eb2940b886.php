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
        echo "<div class=\"mailpoet_tools_slider\">
\t{{#if tools.layerSelector}}
\t\t<a href=\"javascript:;\" class=\"mailpoet_tool mailpoet_newsletter_layer_selector mailpoet_ignore_drag\" title=\"";
        // line 3
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Switch editing layer");
        echo "\">
    \t\t";
        // line 4
        echo \MailPoetVendor\twig_source($this->env, "newsletter/templates/svg/block-tools/settings.svg");
        echo "
\t\t</a>
\t{{/if}}
\t{{#if tools.settings}}
\t\t<a href=\"javascript:;\" data-automation-id=\"settings_tool\" class=\"mailpoet_tool mailpoet_edit_block mailpoet_ignore_drag\" title=\"";
        // line 8
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Edit settings");
        echo "\">
\t\t\t";
        // line 9
        echo \MailPoetVendor\twig_source($this->env, "newsletter/templates/svg/block-tools/settings.svg");
        echo "
\t\t</a>
\t{{/if}}
\t{{#if tools.delete}}
\t\t<div class=\"mailpoet_delete_block mailpoet_ignore_drag\">
\t\t\t<a href=\"javascript:;\" class=\"mailpoet_tool mailpoet_delete_block_activate\" title=\"";
        // line 14
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Delete");
        echo "\">
\t\t\t\t";
        // line 15
        echo \MailPoetVendor\twig_source($this->env, "newsletter/templates/svg/block-tools/trash.svg");
        echo "
\t\t\t</a>
\t\t\t<a href=\"javascript:;\" class=\"mailpoet_delete_block_cancel\" title=\"";
        // line 17
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Cancel deletion");
        echo "\">
\t\t\t\t";
        // line 18
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Cancel");
        echo "
\t\t\t</a>
\t\t\t<a href=\"javascript:;\" class=\"mailpoet_delete_block_confirm\" title=\"";
        // line 20
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Confirm deletion");
        echo "\">
\t\t\t\t";
        // line 21
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Delete");
        echo "
\t\t\t</a>
\t\t</div>
\t{{/if}}
\t{{#if tools.duplicate}}
\t\t<a href=\"javascript:;\" data-automation-id=\"duplicate_tool\" class=\"mailpoet_tool mailpoet_duplicate_block\" title=\"";
        // line 26
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Duplicate");
        echo "\">
\t\t\t";
        // line 27
        echo \MailPoetVendor\twig_source($this->env, "newsletter/templates/svg/block-tools/duplicate.svg");
        echo "
\t\t</a>
\t{{/if}}
\t{{#if tools.move}}
\t\t<a href=\"javascript:;\" class=\"mailpoet_tool mailpoet_move_block\" title=\"";
        // line 31
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Drag to move");
        echo "\">
  \t\t\t";
        // line 32
        echo \MailPoetVendor\twig_source($this->env, "newsletter/templates/svg/block-tools/move.svg");
        echo "
\t\t</a>
\t{{/if}}
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/base/toolsGeneric.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  109 => 32,  105 => 31,  98 => 27,  94 => 26,  86 => 21,  82 => 20,  77 => 18,  73 => 17,  68 => 15,  64 => 14,  56 => 9,  52 => 8,  45 => 4,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/base/toolsGeneric.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/base/toolsGeneric.hbs");
    }
}
