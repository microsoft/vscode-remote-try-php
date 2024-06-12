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

/* form/templatesLegacy/settings/field.hbs */
class __TwigTemplate_75dd3a22a49c2264bafb894c9e7db839d9ecdb9fd7f35c447b6227af578c5da6 extends Template
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
        echo "<form id=\"form_field_settings\" name=\"form_field_settings\" action=\"\" method=\"post\">
  {{#ifCond type 'in' 'submit'}}
    {{> _settings_label }}
  {{/ifCond}}

  {{#ifCond type '==' 'text'}}
    {{> _settings_label }}
    {{> _settings_label_within }}
    {{#ifCond id 'in' 'first_name,last_name' }}
      {{> _settings_required }}
    {{/ifCond}}
  {{/ifCond}}

  {{#ifCond type '==' 'textarea'}}
    {{> _settings_label }}
    {{> _settings_label_within }}

    <p class=\"clearfix\">
      <label>";
        // line 19
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Number of lines:");
        echo "</label>
      <select name=\"params[lines]\">
        ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = \MailPoetVendor\twig_ensure_traversable(range(1, 5));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 22
            echo "          <option value=\"";
            echo \MailPoetVendor\twig_escape_filter($this->env, $context["i"], "html", null, true);
            echo "\"
            {{#ifCond params.lines '==' ";
            // line 23
            echo \MailPoetVendor\twig_escape_filter($this->env, $context["i"], "html", null, true);
            echo "}}selected=\"selected\"{{/ifCond}}
          >";
            // line 24
            echo \MailPoetVendor\twig_escape_filter($this->env, \MailPoetVendor\twig_sprintf($this->extensions['MailPoet\Twig\I18n']->pluralize("1 line", "%d lines", $context["i"]), $context["i"]), "html", null, true);
            echo "</option>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 26
        echo "      </select>
    </p>
  {{/ifCond}}

  {{#ifCond type 'in' 'checkbox,radio'}}
    {{> _settings_label }}
  {{/ifCond}}

  {{#ifCond type '==' 'segment'}}
    {{> _settings_label }}
    {{> _settings_segment_selection }}
  {{/ifCond}}

  {{#ifCond type '==' 'select'}}
    {{> _settings_label }}
    {{> _settings_label_within }}
  {{/ifCond}}

  {{#ifCond type '==' 'date'}}
    {{> _settings_label }}
    {{> _settings_date_default }}
    {{> _settings_date_format }}
  {{/ifCond}}

  {{#ifCond type '==' 'html'}}
    <textarea name=\"params[text]\" class=\"mailpoet_form_field_settings_text\">{{ params.text }}</textarea>
    <p class=\"clearfix\">
      <label>
        <input type=\"hidden\" name=\"params[nl2br]\" value=\"0\" />
        <input
          class=\"mailpoet_checkbox\"
          type=\"checkbox\"
          name=\"params[nl2br]\"
          {{#ifCond params.nl2br \">\" 0}}checked=\"checked\"{{/ifCond}}
          value=\"1\"
          />&nbsp;";
        // line 61
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Automatically add paragraphs");
        echo "
      </label>
    </p>
  {{/ifCond}}

  {{> _settings_submit }}
</form>

<script type=\"text/javascript\">
  jQuery(function(\$) {
    \$(document).on('submit', '#form_field_settings', function(e) {
      // trigger callback
      MailPoet.Modal.success();
      return false;
    });
  });
<{{!}}/script>
";
    }

    public function getTemplateName()
    {
        return "form/templatesLegacy/settings/field.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  120 => 61,  83 => 26,  75 => 24,  71 => 23,  66 => 22,  62 => 21,  57 => 19,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/settings/field.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/settings/field.hbs");
    }
}
