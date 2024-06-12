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

/* woocommerce_setup.html */
class __TwigTemplate_fbbbbad2941fe4d62ead317e87baf3d52e20e55ee17d90822382ddf13ef920bf extends Template
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
        $this->parent = $this->loadTemplate("layout.html", "woocommerce_setup.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "<script>
  var mailpoet_logo_url = '";
        // line 5
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/mailpoet-logo.20200623.png");
        echo "';
  var wizard_woocommerce_illustration_url = '";
        // line 6
        echo $this->extensions['MailPoet\Twig\Assets']->generateCdnUrl("welcome-wizard/woocommerce.20200623.png");
        echo "';
  var mailpoet_show_customers_import = ";
        // line 7
        echo json_encode(($context["show_customers_import"] ?? null));
        echo ";
  var finish_wizard_url = '";
        // line 8
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["finish_wizard_url"] ?? null), "html", null, true);
        echo "';
</script>

<div id=\"mailpoet-wizard-container\"></div>

";
    }

    public function getTemplateName()
    {
        return "woocommerce_setup.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  65 => 8,  61 => 7,  57 => 6,  53 => 5,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "woocommerce_setup.html", "/home/circleci/mailpoet/mailpoet/views/woocommerce_setup.html");
    }
}
