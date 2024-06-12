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

/* experimental-features.html */
class __TwigTemplate_f8cc740392ae60b79073d86d04178d1110950ce925efc059b30bbb77f1d5cf0f extends Template
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
        $this->parent = $this->loadTemplate("layout.html", "experimental-features.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "
<div class=\"wrap\">
  <h1 class=\"mailpoet-h1\">Experimental features</h1>

  <div class=\"mailpoet_notice notice notice-error\">
    <p>
      <strong>These features are not finished, they are not meant to be used yet.</strong>
    </p>
    <p>
      If you enable them anything can happen: your website may go down,
      all your data can be deleted.
    </p>
    <p>
      We are not liable.
    </p>
  </div>

  <div id=\"experimental_features_container\"></div>
</div>

";
    }

    public function getTemplateName()
    {
        return "experimental-features.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "experimental-features.html", "/home/circleci/mailpoet/mailpoet/views/experimental-features.html");
    }
}
