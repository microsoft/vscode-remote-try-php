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

/* newsletter/templates/components/history.hbs */
class __TwigTemplate_79810918dede10b67349ebd5005561b67aec3910cc375e8ac5a9175e7ad92e4b extends Template
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
        echo "<div class=\"mailpoet_history_wrapper\">
  <a id=\"mailpoet-history-arrow-undo\" class=\"mailpoet_history_arrow\">
    <svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"miter\" fill=\"none\">
      <path d=\"M5,17 L5,15 C5,10.0294373 8.80557963,6 13.5,6 C18.1944204,6 22,10.0294373 22,15\"/>
      <polyline points=\"8 15 5 18 2 15\"/>
    </svg>
  </a>

  <a id=\"mailpoet-history-arrow-redo\" class=\"mailpoet_history_arrow\">
    <svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"miter\" fill=\"none\">
      <path d=\"M19,17 L19,15 C19,10.0294373 15.1944204,6 10.5,6 C5.80557963,6 2,10.0294373 2,15\"/>
      <polyline points=\"16 15 19 18 22 15\"/>
    </svg>
  </a>
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/components/history.hbs";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/components/history.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/components/history.hbs");
    }
}
