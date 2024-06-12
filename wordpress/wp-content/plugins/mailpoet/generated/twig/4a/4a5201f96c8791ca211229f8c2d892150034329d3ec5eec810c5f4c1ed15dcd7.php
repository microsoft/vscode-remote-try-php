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

/* form/templatesLegacy/settings/validate.hbs */
class __TwigTemplate_f59056ef084bac041a68994e1a26c4b21e498ba3e076f6d34e090e442bd4bc4e extends Template
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
  <label>";
        // line 2
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Validate for:");
        echo "</label>
  <select name=\"params[validate]\">
    <option {{#ifCond params.validate '==' ''}}selected=\"selected\"{{/ifCond}}
      value=\"\">
      ";
        // line 6
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Nothing");
        echo "
    </option>

    <option {{#ifCond params.validate '==' 'number'}}selected=\"selected\"{{/ifCond}}
      value=\"number\">
      ";
        // line 11
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Numbers only");
        echo "
    </option>

    <option {{#ifCond params.validate '==' 'alphanum'}}selected=\"selected\"{{/ifCond}}
      value=\"alphanum\">
      ";
        // line 16
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Alphanumerical");
        echo "
    </option>

    <option {{#ifCond params.validate '==' 'phone'}}selected=\"selected\"{{/ifCond}}
      value=\"phone\">
      ";
        // line 21
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Phone number, (+,-,#,(,) and spaces allowed)");
        echo "
    </option>
  </select>
</p>";
    }

    public function getTemplateName()
    {
        return "form/templatesLegacy/settings/validate.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 21,  63 => 16,  55 => 11,  47 => 6,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/settings/validate.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/settings/validate.hbs");
    }
}
