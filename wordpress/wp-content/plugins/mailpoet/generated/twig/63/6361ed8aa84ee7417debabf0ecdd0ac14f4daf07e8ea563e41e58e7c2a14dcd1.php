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

/* form/templatesLegacy/settings/label.hbs */
class __TwigTemplate_988bff1dab18e4a9fc1682f0deca0fbcffaad5afb9f1d4cb9e02875450d44f10 extends Template
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
        echo "<p class=\"clearfix\">
  <label for=\"label\">";
        // line 2
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Label:");
        echo "</label>
  <input id=\"label\" type=\"text\" name=\"params[label]\" value=\"{{ params.label }}\" />
</p>";
    }

    public function getTemplateName()
    {
        return "form/templatesLegacy/settings/label.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/settings/label.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/settings/label.hbs");
    }
}
