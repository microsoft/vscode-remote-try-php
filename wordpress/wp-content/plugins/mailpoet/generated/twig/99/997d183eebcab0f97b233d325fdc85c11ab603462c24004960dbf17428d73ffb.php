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

/* automation/analytics.html */
class __TwigTemplate_ab899d530f98e746914257e34f50c70b47be9222f8900b262c8fc7567efcd81e extends Template
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
        $this->parent = $this->loadTemplate("layout.html", "automation/analytics.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "<div id=\"mailpoet_automation_analytics\" class=\"woocommerce-page\"></div>

<script type=\"text/javascript\">
  var mailpoet_locale_full = ";
        // line 7
        echo json_encode(($context["locale_full"] ?? null));
        echo ";
  var mailpoet_automation_api = ";
        // line 8
        echo json_encode(($context["api"] ?? null));
        echo ";
  var mailpoet_json_api = ";
        // line 9
        echo json_encode(($context["jsonapi"] ?? null));
        echo ";
  var mailpoet_automation_registry = ";
        // line 10
        echo json_encode(($context["registry"] ?? null));
        echo ";
  var mailpoet_automation_context = ";
        // line 11
        echo json_encode(($context["context"] ?? null));
        echo ";
  var mailpoet_automation = ";
        // line 12
        echo ((($context["automation"] ?? null)) ? (json_encode(($context["automation"] ?? null))) : ("undefined"));
        echo ";
</script>
";
    }

    public function getTemplateName()
    {
        return "automation/analytics.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  75 => 12,  71 => 11,  67 => 10,  63 => 9,  59 => 8,  55 => 7,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "automation/analytics.html", "/home/circleci/mailpoet/mailpoet/views/automation/analytics.html");
    }
}
