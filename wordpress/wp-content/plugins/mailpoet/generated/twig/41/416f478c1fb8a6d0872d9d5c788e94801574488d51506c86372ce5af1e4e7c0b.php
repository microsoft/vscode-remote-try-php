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

/* newsletter/templates/components/sidebar/sidebar.hbs */
class __TwigTemplate_93e1b105309beaac83ec57a4590bfeb49947fba3e23fcb02639f4c6f538e4ba9 extends Template
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
        yield "<div class=\"mailpoet_sidebar_region\" id=\"mailpoet_editor_history\"></div>
<div class=\"mailpoet_region mailpoet_content_region mailpoet_sidebar_region postbox {{#if isWoocommerceTransactional}} closed {{/if}}\"></div>
<div class=\"mailpoet_region mailpoet_layout_region mailpoet_sidebar_region postbox closed\"></div>
<div class=\"mailpoet_region mailpoet_styles_region mailpoet_sidebar_region postbox {{#if isWoocommerceTransactional}} {{else}} closed {{/if}}\"></div>
<div class=\"mailpoet_region mailpoet_preview_region mailpoet_sidebar_region postbox closed{{#if isWoocommerceTransactional}}  mailpoet_hidden{{/if}}\"></div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/components/sidebar/sidebar.hbs";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array ();
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/components/sidebar/sidebar.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/components/sidebar/sidebar.hbs");
    }
}
