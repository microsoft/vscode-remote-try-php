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

/* newsletter/templates/blocks/products/settings.hbs */
class __TwigTemplate_1114a669d366cd147bdad7cb9fbcaa0f2a72a035162ab08841589ba0a1343a6a extends Template
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
        yield "<h3>";
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Product selection");
        yield "</h3>
<div class=\"mailpoet_settings_products_selection\"></div>
<div class=\"mailpoet_settings_products_display_options mailpoet_closed\"></div>
<div class=\"mailpoet_settings_products_controls\">
  <div class=\"mailpoet_form_field\">
      <a href=\"javascript:;\" class=\"mailpoet_settings_products_show_product_selection mailpoet_hidden\">";
        // line 6
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Back to selection");
        yield "</a>
      <a href=\"javascript:;\" class=\"mailpoet_settings_products_show_display_options\">";
        // line 7
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Display options");
        yield "</a>
  </div>
  <div class=\"mailpoet_form_field\">
    <input type=\"button\" class=\"button button-primary mailpoet_settings_products_insert_selected\" value=\"";
        // line 10
        yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($this->extensions['MailPoet\Twig\I18n']->translate("Insert selected"), "html_attr");
        yield "\" />
  </div>
</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "newsletter/templates/blocks/products/settings.hbs";
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
        return array (  57 => 10,  51 => 7,  47 => 6,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/products/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/products/settings.hbs");
    }
}
