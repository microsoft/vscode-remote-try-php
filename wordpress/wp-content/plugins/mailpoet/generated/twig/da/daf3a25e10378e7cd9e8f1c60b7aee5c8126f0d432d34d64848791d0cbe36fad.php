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

/* form/templatesLegacy/settings/field_form.hbs */
class __TwigTemplate_3297f258525111f56ede2b0258ec6596abe1eaa0884606e0232c86fa47e90ec7 extends Template
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
        yield "
<form
  id=\"form_field_new\"
  name=\"form_field_new\"
  action=\"\"
  method=\"post\"
  data-parsley-validate=\"true\"
>
  {{#if id}}<input type=\"hidden\" id=\"field_id\" name=\"id\" value=\"{{ id }}\" />{{/if}}
  <p>
    <label for=\"field_type\">";
        // line 11
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Select a field type:");
        yield "</label>
    <select
      id=\"field_type\"
      name=\"type\"
      data-parsley-required=\"true\"
      data-parsley-required-message=\"";
        // line 16
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Please specify a type.");
        yield "\"
    >
      <option value=\"\">--</option>
      <option
        {{#ifCond type '==' 'text'}}selected=\"selected\"{{/ifCond}}
        value=\"text\">";
        // line 21
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Text Input");
        yield "
      </option>
      <option
        {{#ifCond type '==' 'textarea'}}selected=\"selected\"{{/ifCond}}
        value=\"textarea\">";
        // line 25
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Text Area");
        yield "
      </option>
      <option
        {{#ifCond type '==' 'radio'}}selected=\"selected\"{{/ifCond}}
        value=\"radio\">";
        // line 29
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Radio buttons");
        yield "
      </option>
      <option
        {{#ifCond type '==' 'checkbox'}}selected=\"selected\"{{/ifCond}}
        value=\"checkbox\">";
        // line 33
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Checkbox");
        yield "
      </option>
      <option
        {{#ifCond type '==' 'select'}}selected=\"selected\"{{/ifCond}}
        value=\"select\">";
        // line 37
        yield $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Select", "Form input type");
        yield "
      </option>
      <option
        {{#ifCond type '==' 'date'}}selected=\"selected\"{{/ifCond}}
        value=\"date\">";
        // line 41
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Date");
        yield "
      </option>
    </select>
  </p>
  <p>
    <label for=\"field_name\">";
        // line 46
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Field name:");
        yield "</label>
    <input
      id=\"field_name\"
      type=\"text\"
      name=\"name\"
      value=\"{{ name }}\"
      data-parsley-required=\"true\"
      data-parsley-required-message=\"";
        // line 53
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Please specify a name.");
        yield "\"
    />
  </p>
  <hr />

  <div class=\"field_type_form\"></div>

  <p class=\"mailpoet_align_right\">
    <input type=\"submit\" value=\"";
        // line 61
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Done");
        yield "\" class=\"button-primary\" />
  </p>
</form>

<script type=\"text/javascript\">
  jQuery(function(\$) {

    \$(function() {
      loadFieldForm();
    });

    \$('#form_field_new #field_type').on('change', function() {
      loadFieldForm(\$(this).val());
    });

    function loadFieldForm(type) {
      type = (type === undefined) ? \$('#form_field_new #field_type').val() : type;
      if(type !== '') {
        var template = Handlebars.compile(\$('#form_template_field_'+type).html()),
            data = {type: type},
            field_id = \$('#form_field_new #field_id').val();

        if(field_id !== undefined && field_id.length > 0) {
          var params = \$('.mailpoet_form_field[wysija_id=\"'+field_id+'\"]').attr('wysija_params');
          if(params !== undefined) {
            data.params = JSON.parse(params);
          }
        }
        // render field template
        \$('#form_field_new .field_type_form').html(template(data));
      } else {
        \$('#form_field_new .field_type_form').html('');
      }
    }
  });
</script>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "form/templatesLegacy/settings/field_form.hbs";
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
        return array (  130 => 61,  119 => 53,  109 => 46,  101 => 41,  94 => 37,  87 => 33,  80 => 29,  73 => 25,  66 => 21,  58 => 16,  50 => 11,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/settings/field_form.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/settings/field_form.hbs");
    }
}
