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

/* form/templatesLegacy/blocks/date.hbs */
class __TwigTemplate_a9317f1fb35a20ab77b213addbcaeb51cabea35d125194be31ba5f1e8c0cc1b4 extends Template
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
        echo "{{#if params.label}}
  <p>
    <label>{{ params.label }}{{#if params.required}} *{{/if}}</label>
  </p>
{{/if}}
{{#ifCond params.date_type \"==\" \"year_month_day\"}}
  {{#unless params.date_format}}
    {{> _settings_date_months }}
    {{> _settings_date_days }}
    {{> _settings_date_years }}
  {{/unless}}
  {{#ifCond params.date_format \"==\" \"MM/DD/YYYY\"}}
    {{> _settings_date_months }}
    {{> _settings_date_days }}
    {{> _settings_date_years }}
  {{/ifCond}}
  {{#ifCond params.date_format \"==\" \"DD/MM/YYYY\"}}
    {{> _settings_date_days }}
    {{> _settings_date_months }}
    {{> _settings_date_years }}
  {{/ifCond}}
  {{#ifCond params.date_format \"==\" \"YYYY/MM/DD\"}}
    {{> _settings_date_years }}
    {{> _settings_date_months }}
    {{> _settings_date_days }}
  {{/ifCond}}
{{/ifCond}}

{{#ifCond params.date_type \"==\" \"year_month\"}}
  {{#unless params.date_format}}
    {{> _settings_date_months }}
    {{> _settings_date_years }}
  {{/unless}}
  {{#ifCond params.date_format \"==\" \"MM/YYYY\"}}
    {{> _settings_date_months }}
    {{> _settings_date_years }}
  {{/ifCond}}
  {{#ifCond params.date_format \"==\" \"YYYY/MM\"}}
    {{> _settings_date_years }}
    {{> _settings_date_months }}
  {{/ifCond}}
{{/ifCond}}

{{#ifCond params.date_type \"==\" \"year\"}}
  {{> _settings_date_years }}
{{/ifCond}}

{{#ifCond params.date_type \"==\" \"month\"}}
  {{> _settings_date_months }}
{{/ifCond}}
";
    }

    public function getTemplateName()
    {
        return "form/templatesLegacy/blocks/date.hbs";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/blocks/date.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/blocks/date.hbs");
    }
}
