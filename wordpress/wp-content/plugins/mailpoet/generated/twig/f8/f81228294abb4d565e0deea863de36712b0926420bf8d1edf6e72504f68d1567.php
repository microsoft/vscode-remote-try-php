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

/* form/template_selection.html */
class __TwigTemplate_72cb81adca1fcada3d740fa851dd9cdc07463639615e596a1f2d37bd9b4fa4aa extends Template
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
        $this->parent = $this->loadTemplate("layout.html", "form/template_selection.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "<div class=\"block-editor\">
  <div id=\"mailpoet_form_edit_templates\">
  </div>
</div>

<script>
  ";
        // line 11
        echo "  var mailpoet_templates = ";
        echo json_encode(($context["templates"] ?? null));
        echo ";
  var mailpoet_form_edit_url =
    \"";
        // line 13
        echo admin_url("admin.php?page=mailpoet-form-editor&template_id=");
        echo "\";
  ";
        // line 15
        echo "</script>

<style id=\"mailpoet-form-editor-form-styles\"></style>
";
    }

    public function getTemplateName()
    {
        return "form/template_selection.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 15,  64 => 13,  58 => 11,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/template_selection.html", "/home/circleci/mailpoet/mailpoet/views/form/template_selection.html");
    }
}
