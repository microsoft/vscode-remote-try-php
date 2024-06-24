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
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "<div id=\"mailpoet_automation_analytics\" class=\"woocommerce-page\"></div>

<script type=\"text/javascript\">
  var mailpoet_locale_full = ";
        // line 7
        yield json_encode(($context["locale_full"] ?? null));
        yield ";
  var mailpoet_automation_api = ";
        // line 8
        yield json_encode(($context["api"] ?? null));
        yield ";
  var mailpoet_json_api = ";
        // line 9
        yield json_encode(($context["jsonapi"] ?? null));
        yield ";
  var mailpoet_automation_registry = ";
        // line 10
        yield json_encode(($context["registry"] ?? null));
        yield ";
  var mailpoet_automation_context = ";
        // line 11
        yield json_encode(($context["context"] ?? null));
        yield ";
  var mailpoet_automation = ";
        // line 12
        yield ((($context["automation"] ?? null)) ? (json_encode(($context["automation"] ?? null))) : ("undefined"));
        yield ";
</script>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "automation/analytics.html";
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
        return array (  76 => 12,  72 => 11,  68 => 10,  64 => 9,  60 => 8,  56 => 7,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "automation/analytics.html", "/home/circleci/mailpoet/mailpoet/views/automation/analytics.html");
    }
}
