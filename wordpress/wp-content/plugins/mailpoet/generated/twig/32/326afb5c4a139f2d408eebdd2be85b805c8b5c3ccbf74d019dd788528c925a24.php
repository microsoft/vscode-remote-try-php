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

/* landingpage.html */
class __TwigTemplate_1453f1606febf8a9b6e30a893bcc22827936ceb21531b5a8f8a196260a5efc5a extends Template
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
        $this->parent = $this->loadTemplate("layout.html", "landingpage.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "<div id=\"mailpoet_landingpage_container\"></div>

<script type=\"text/javascript\">
  ";
        // line 8
        echo "    var mailpoet_welcome_wizard_url = ";
        echo json_encode(($context["welcome_wizard_url"] ?? null));
        echo ";
    var mailpoet_welcome_wizard_current_step = ";
        // line 9
        echo json_encode(($context["welcome_wizard_current_step"] ?? null));
        echo ";
  ";
        // line 11
        echo "</script>
";
    }

    public function getTemplateName()
    {
        return "landingpage.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  64 => 11,  60 => 9,  55 => 8,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "landingpage.html", "/home/circleci/mailpoet/mailpoet/views/landingpage.html");
    }
}
