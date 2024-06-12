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

/* logs.html */
class __TwigTemplate_176225ef6518abb3a235b9f1785c332f068c4c798b602f397b3f290225c58508 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "layout.html";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("layout.html", "logs.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "
<div class=\"wrap\">
  <h1 class=\"mailpoet-h1\">Logs</h1>

  <div id=\"mailpoet_logs_container\"></div>

  <script type=\"text/javascript\">
    ";
        // line 12
        echo "      var mailpoet_logs = ";
        echo json_encode(($context["logs"] ?? null));
        echo ";
    ";
        // line 14
        echo "  </script>
</div>

";
        // line 17
        echo $this->extensions['MailPoet\Twig\I18n']->localize(["pageTitle" => $this->extensions['MailPoet\Twig\I18n']->translate("Logs"), "tableHeaderName" => $this->extensions['MailPoet\Twig\I18n']->translate("Name"), "tableHeaderMessage" => $this->extensions['MailPoet\Twig\I18n']->translate("Message"), "tableHeaderCreatedOn" => $this->extensions['MailPoet\Twig\I18n']->translate("Created On"), "searchLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("Search"), "offsetLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("Offset"), "limitLabel" => $this->extensions['MailPoet\Twig\I18n']->translate("Limit"), "from" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("From", "date from"), "to" => $this->extensions['MailPoet\Twig\I18n']->translateWithContext("To", "date to"), "filter" => $this->extensions['MailPoet\Twig\I18n']->translate("Filter")]);
        // line 28
        echo "

";
    }

    public function getTemplateName()
    {
        return "logs.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 28,  69 => 17,  64 => 14,  59 => 12,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "logs.html", "/home/circleci/mailpoet/mailpoet/views/logs.html");
    }
}
