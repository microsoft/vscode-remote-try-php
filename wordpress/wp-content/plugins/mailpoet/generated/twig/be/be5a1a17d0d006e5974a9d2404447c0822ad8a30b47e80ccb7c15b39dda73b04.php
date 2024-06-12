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

/* woocommerce/settings_overlay.html */
class __TwigTemplate_c96deca159bff652d060c28604f142c91e89f1bcbd66cfad37714d1bb440dcae extends Template
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
        echo "<style>
  /* Hide WooCommerce section with template styling */
  #email_template_options-description + .form-table {
    opacity: 0.2;
    pointer-events: none;
  }

  /* Position MailPoet buttons over hidden table */
  .mailpoet-woocommerce-email-overlay {
    bottom: 320px;
    left: 0;
    max-width: 100%;
    text-align: left;
    position: absolute;
    text-align: center;
    width: 640px;
    z-index: 1;
  }
</style>

<div class=\"mailpoet-woocommerce-email-overlay\">
  <a class=\"button button-primary\"
    href=\"?page=mailpoet-newsletter-editor&id=";
        // line 23
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["woocommerce_template_id"] ?? null), "html", null, true);
        echo "\"
    data-automation-id=\"mailpoet_woocommerce_customize\"
  >
  \t";
        // line 26
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Customize with MailPoet", "Button in WooCommerce settings page");
        echo "
  </a>
  <br>
  <br>
  <a href=\"?page=mailpoet-settings#woocommerce\" data-automation-id=\"mailpoet_woocommerce_disable\">
    ";
        // line 31
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Disable MailPoet customizer", "Link from WooCommerce plugin to MailPoet");
        echo "
  </a>
</div>
";
    }

    public function getTemplateName()
    {
        return "woocommerce/settings_overlay.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  75 => 31,  67 => 26,  61 => 23,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "woocommerce/settings_overlay.html", "/home/circleci/mailpoet/mailpoet/views/woocommerce/settings_overlay.html");
    }
}
