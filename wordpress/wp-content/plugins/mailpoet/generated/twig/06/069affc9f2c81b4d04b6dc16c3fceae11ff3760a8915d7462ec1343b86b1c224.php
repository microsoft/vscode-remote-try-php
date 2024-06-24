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

/* form/custom_fields_legacy.html */
class __TwigTemplate_e8157be6d3c1ea26ac50aff90bbbee2f89ea00cb3e7ca20d733f2850ddba43fe extends Template
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
        yield "<!-- date settings and block templates -->
";
        // line 2
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "form_template_date_years", "form/templatesLegacy/blocks/date_years.hbs", "_settings_date_years");
        // line 5
        yield "
";
        // line 6
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "form_template_date_months", "form/templatesLegacy/blocks/date_months.hbs", "_settings_date_months");
        // line 9
        yield "
";
        // line 10
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "form_template_date_days", "form/templatesLegacy/blocks/date_days.hbs", "_settings_date_days");
        // line 13
        yield "
";
        // line 14
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "form_template_date", "form/templatesLegacy/blocks/date.hbs");
        yield "

<!-- field settings -->
";
        // line 17
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "form_template_field_settings", "form/templatesLegacy/settings/field.hbs");
        yield "

";
        // line 19
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_label", "form/templatesLegacy/settings/label.hbs", "_settings_label");
        // line 22
        yield "
";
        // line 23
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_label_within", "form/templatesLegacy/settings/label_within.hbs", "_settings_label_within");
        // line 26
        yield "
";
        // line 27
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_required", "form/templatesLegacy/settings/required.hbs", "_settings_required");
        // line 30
        yield "
";
        // line 31
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_validate", "form/templatesLegacy/settings/validate.hbs", "_settings_validate");
        // line 34
        yield "
";
        // line 35
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_values", "form/templatesLegacy/settings/values.hbs", "_settings_values");
        // line 38
        yield "
";
        // line 39
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_date_default", "form/templatesLegacy/settings/date_default.hbs", "_settings_date_default");
        // line 42
        yield "
";
        // line 43
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_submit", "form/templatesLegacy/settings/submit.hbs", "_settings_submit");
        // line 46
        yield "

";
        // line 48
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_values_item", "form/templatesLegacy/settings/values_item.hbs");
        // line 49
        yield "
";
        // line 50
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_date_format", "form/templatesLegacy/settings/date_formats.hbs", "_settings_date_format");
        // line 54
        yield "
";
        // line 55
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_date_type", "form/templatesLegacy/settings/date_types.hbs", "_settings_date_type");
        // line 59
        yield "
";
        // line 60
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_segment_selection_item", "form/templatesLegacy/settings/segment_selection_item.hbs");
        // line 62
        yield "
";
        // line 63
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "field_settings_segment_selection", "form/templatesLegacy/settings/segment_selection.hbs", "_settings_segment_selection");
        // line 66
        yield "

<!-- custom field: new -->
";
        // line 69
        yield $this->extensions['MailPoet\Twig\Handlebars']->generatePartial($this->env, $context, "form_template_field_form", "form/templatesLegacy/settings/field_form.hbs");
        // line 71
        yield "

<!-- field settings depending on field type -->
<script id=\"form_template_field_text\" type=\"text/x-handlebars-template\">
  {{> _settings_required }}
  {{> _settings_validate }}
</script>

<script id=\"form_template_field_textarea\" type=\"text/x-handlebars-template\">
  {{> _settings_required }}
  {{> _settings_validate }}
</script>

<script id=\"form_template_field_radio\" type=\"text/x-handlebars-template\">
  {{> _settings_values }}
  {{> _settings_required }}
</script>

<script id=\"form_template_field_checkbox\" type=\"text/x-handlebars-template\">
  {{> _settings_values }}
  {{> _settings_required }}
</script>

<script id=\"form_template_field_select\" type=\"text/x-handlebars-template\">
  {{> _settings_values }}
  {{> _settings_required }}
</script>

<script id=\"form_template_field_date\" type=\"text/x-handlebars-template\">
  {{> _settings_required }}
  {{> _settings_date_type }}
</script>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "form/custom_fields_legacy.html";
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
        return array (  132 => 71,  130 => 69,  125 => 66,  123 => 63,  120 => 62,  118 => 60,  115 => 59,  113 => 55,  110 => 54,  108 => 50,  105 => 49,  103 => 48,  99 => 46,  97 => 43,  94 => 42,  92 => 39,  89 => 38,  87 => 35,  84 => 34,  82 => 31,  79 => 30,  77 => 27,  74 => 26,  72 => 23,  69 => 22,  67 => 19,  62 => 17,  56 => 14,  53 => 13,  51 => 10,  48 => 9,  46 => 6,  43 => 5,  41 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/custom_fields_legacy.html", "/home/circleci/mailpoet/mailpoet/views/form/custom_fields_legacy.html");
    }
}
