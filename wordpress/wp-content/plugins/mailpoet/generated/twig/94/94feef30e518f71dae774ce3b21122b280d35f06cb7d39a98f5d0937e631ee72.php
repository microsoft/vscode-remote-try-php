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

/* automation.html */
class __TwigTemplate_5c71d57f60a0f5a4fc5d48d80259463fe103c80202fce3a75da88803acda1c77 extends Template
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
        $this->parent = $this->loadTemplate("layout.html", "automation.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "<div id=\"mailpoet_automation\"></div>

<script type=\"text/javascript\">
  var mailpoet_locale_full = ";
        // line 7
        echo json_encode(($context["locale_full"] ?? null));
        echo ";
  var mailpoet_automation_api = ";
        // line 8
        echo json_encode(($context["api"] ?? null));
        echo ";
  var mailpoet_automation_count = ";
        // line 9
        echo json_encode(($context["automationCount"] ?? null));
        echo ";
  var mailpoet_legacy_automation_count = ";
        // line 10
        echo json_encode(($context["legacyAutomationCount"] ?? null));
        echo ";
  var mailpoet_automation_templates = ";
        // line 11
        echo json_encode(($context["templates"] ?? null));
        echo ";
  var mailpoet_automation_template_categories = ";
        // line 12
        echo json_encode(($context["template_categories"] ?? null));
        echo ";
  var mailpoet_automation_registry = ";
        // line 13
        echo json_encode(($context["registry"] ?? null));
        echo ";
  var mailpoet_automation_context = ";
        // line 14
        echo json_encode(($context["context"] ?? null));
        echo ";
  var mailpoet_segments = ";
        // line 15
        echo json_encode(($context["segments"] ?? null));
        echo ";
  var mailpoet_roles = ";
        // line 16
        echo json_encode(($context["roles"] ?? null));
        echo ";
  var mailpoet_woocommerce_automatic_emails = ";
        // line 17
        echo json_encode(($context["automatic_emails"] ?? null));
        echo ";
  var mailpoet_legacy_automations_notice_dismissed = ";
        // line 18
        echo json_encode(($context["legacy_automations_notice_dismissed"] ?? null));
        echo ";
</script>
";
    }

    public function getTemplateName()
    {
        return "automation.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  99 => 18,  95 => 17,  91 => 16,  87 => 15,  83 => 14,  79 => 13,  75 => 12,  71 => 11,  67 => 10,  63 => 9,  59 => 8,  55 => 7,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "automation.html", "/home/circleci/mailpoet/mailpoet/views/automation.html");
    }
}
