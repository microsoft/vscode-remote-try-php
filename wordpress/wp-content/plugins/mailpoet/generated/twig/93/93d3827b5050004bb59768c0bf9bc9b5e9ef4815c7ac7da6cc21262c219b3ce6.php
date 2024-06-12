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

/* newsletter/templates/blocks/container/emptyBlock.hbs */
class __TwigTemplate_49dbfe4146cc56abca0fd262ba63938d36f1ee75a2835926df2a4ac731461dc3 extends Template
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
        echo "<div class=\"mailpoet_container_empty\">{{#ifCond emptyContainerMessage '!==' ''}}{{emptyContainerMessage}}{{else}}{{#if isRoot}}";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Add a column block here.");
        echo "{{else}}";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Add a content block here.");
        echo "{{/if}}{{/ifCond}}</div>
{{debug}}
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/container/emptyBlock.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/container/emptyBlock.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/container/emptyBlock.hbs");
    }
}
