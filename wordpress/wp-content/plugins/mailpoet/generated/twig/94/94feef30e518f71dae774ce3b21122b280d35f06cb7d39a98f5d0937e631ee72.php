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
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "<div id=\"mailpoet_automation\"></div>

<script type=\"text/javascript\">
  var mailpoet_locale_full = ";
        // line 7
        yield json_encode(($context["locale_full"] ?? null));
        yield ";
  var mailpoet_automation_api = ";
        // line 8
        yield json_encode(($context["api"] ?? null));
        yield ";
  var mailpoet_automation_count = ";
        // line 9
        yield json_encode(($context["automationCount"] ?? null));
        yield ";
  var mailpoet_legacy_automation_count = ";
        // line 10
        yield json_encode(($context["legacyAutomationCount"] ?? null));
        yield ";
  var mailpoet_automation_templates = ";
        // line 11
        yield json_encode(($context["templates"] ?? null));
        yield ";
  var mailpoet_automation_template_categories = ";
        // line 12
        yield json_encode(($context["template_categories"] ?? null));
        yield ";
  var mailpoet_automation_registry = ";
        // line 13
        yield json_encode(($context["registry"] ?? null));
        yield ";
  var mailpoet_automation_context = ";
        // line 14
        yield json_encode(($context["context"] ?? null));
        yield ";
  var mailpoet_segments = ";
        // line 15
        yield json_encode(($context["segments"] ?? null));
        yield ";
  var mailpoet_roles = ";
        // line 16
        yield json_encode(($context["roles"] ?? null));
        yield ";
  var mailpoet_woocommerce_automatic_emails = ";
        // line 17
        yield json_encode(($context["automatic_emails"] ?? null));
        yield ";
  var mailpoet_legacy_automations_notice_dismissed = ";
        // line 18
        yield json_encode(($context["legacy_automations_notice_dismissed"] ?? null));
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
        return "automation.html";
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
        return array (  100 => 18,  96 => 17,  92 => 16,  88 => 15,  84 => 14,  80 => 13,  76 => 12,  72 => 11,  68 => 10,  64 => 9,  60 => 8,  56 => 7,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "automation.html", "/home/circleci/mailpoet/mailpoet/views/automation.html");
    }
}
