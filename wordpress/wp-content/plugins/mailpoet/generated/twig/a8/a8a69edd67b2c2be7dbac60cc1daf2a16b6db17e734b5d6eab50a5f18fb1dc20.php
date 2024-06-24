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

/* automation/templates.html */
class __TwigTemplate_53d600d8a07aac1f252dba6865bc3c701e94cb6909b20e23d47b9ab03d29823c extends Template
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
        $this->parent = $this->loadTemplate("layout.html", "automation/templates.html", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "<div id=\"mailpoet_automation_templates\"></div>

<script type=\"text/javascript\">
  var mailpoet_locale_full = ";
        // line 7
        yield json_encode(($context["locale_full"] ?? null));
        yield ";
  var mailpoet_automation_api = ";
        // line 8
        yield json_encode(($context["api"] ?? null));
        yield ";
  var mailpoet_automation_templates = ";
        // line 9
        yield json_encode(($context["templates"] ?? null));
        yield ";
  var mailpoet_automation_template_categories = ";
        // line 10
        yield json_encode(($context["template_categories"] ?? null));
        yield ";
  var mailpoet_automation_registry = ";
        // line 11
        yield json_encode(($context["registry"] ?? null));
        yield ";
  var mailpoet_automation_context = ";
        // line 12
        yield json_encode(($context["context"] ?? null));
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
        return "automation/templates.html";
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
        return new Source("", "automation/templates.html", "/home/circleci/mailpoet/mailpoet/views/automation/templates.html");
    }
}
