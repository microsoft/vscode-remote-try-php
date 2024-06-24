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
        yield "<p class=\"clearfix\">
  <label>";
        // line 2
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Validate for:");
        yield "</label>
  <select name=\"params[validate]\">
    <option {{#ifCond params.validate '==' ''}}selected=\"selected\"{{/ifCond}}
      value=\"\">
      ";
        // line 6
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Nothing");
        yield "
    </option>

    <option {{#ifCond params.validate '==' 'number'}}selected=\"selected\"{{/ifCond}}
      value=\"number\">
      ";
        // line 11
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Numbers only");
        yield "
    </option>

    <option {{#ifCond params.validate '==' 'alphanum'}}selected=\"selected\"{{/ifCond}}
      value=\"alphanum\">
      ";
        // line 16
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Alphanumerical");
        yield "
    </option>

    <option {{#ifCond params.validate '==' 'phone'}}selected=\"selected\"{{/ifCond}}
      value=\"phone\">
      ";
        // line 21
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Phone number, (+,-,#,(,) and spaces allowed)");
        yield "
    </option>
  </select>
</p>";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "form/templatesLegacy/settings/validate.hbs";
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
        return array (  72 => 21,  64 => 16,  56 => 11,  48 => 6,  41 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/settings/validate.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/settings/validate.hbs");
    }
}
