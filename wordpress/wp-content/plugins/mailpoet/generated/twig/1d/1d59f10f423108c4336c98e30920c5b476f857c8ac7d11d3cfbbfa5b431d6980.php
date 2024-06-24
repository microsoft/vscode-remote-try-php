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

/* form/templatesLegacy/settings/date_default.hbs */
class __TwigTemplate_b3250ae4fa6cf3a8ba59dcf87bbed920729d3d0ed83a6f7fb8ac83124c949d67 extends Template
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
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Preselect today's date:");
        yield "</label>
  <span class=\"group\">
    <label>
      <input
        class=\"mailpoet_radio\"
        type=\"radio\"
        name=\"params[is_default_today]\"
        value=\"1\"
        {{#if params.is_default_today}}checked=\"checked\"{{/if}}
      />";
        // line 11
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Yes");
        yield "
    </label>
    <label>
      <input
        class=\"mailpoet_radio\"
        type=\"radio\"
        name=\"params[is_default_today]\"
        value=\"\"
        {{#unless params.is_default_today}}checked=\"checked\"{{/unless}}
      />";
        // line 20
        yield $this->extensions['MailPoet\Twig\I18n']->translate("No");
        yield "
    </label>
  </span>
</p>";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "form/templatesLegacy/settings/date_default.hbs";
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
        return array (  65 => 20,  53 => 11,  41 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/settings/date_default.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/settings/date_default.hbs");
    }
}
