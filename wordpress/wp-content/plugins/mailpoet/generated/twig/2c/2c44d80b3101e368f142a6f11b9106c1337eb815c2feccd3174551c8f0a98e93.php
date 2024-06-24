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

/* form/templatesLegacy/settings/date_types.hbs */
class __TwigTemplate_e09d68cf316097fd5135112267ebb032f67c0f65a0d93eb763abf51281c496ca extends Template
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
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Type of date");
        yield "</label>
  <select name=\"params[date_type]\">
    ";
        // line 4
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["date_types"] ?? null));
        foreach ($context['_seq'] as $context["type"] => $context["label"]) {
            // line 5
            yield "      <option
        {{#ifCond params.date_type \"==\" \"";
            // line 6
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($context["type"], "html", null, true);
            yield "\"}}
          selected=\"selected\"
        {{/ifCond}}
        data-format=\"";
            // line 9
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape((($__internal_compile_0 = (($__internal_compile_1 = ($context["date_formats"] ?? null)) && is_array($__internal_compile_1) || $__internal_compile_1 instanceof ArrayAccess ? ($__internal_compile_1[$context["type"]] ?? null) : null)) && is_array($__internal_compile_0) || $__internal_compile_0 instanceof ArrayAccess ? ($__internal_compile_0[0] ?? null) : null), "html", null, true);
            yield "\" value=\"";
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($context["type"], "html", null, true);
            yield "\"
      >";
            // line 10
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($context["label"], "html", null, true);
            yield "</option>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['type'], $context['label'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 12
        yield "  </select>
  <input type=\"hidden\" name=\"params[date_format]\" value=\"\" />
</p>

<script type=\"text/javascript\">
  jQuery(function(\$) {
    \$('select[name=\"params[date_type]\"]').on('change', function() {
      // set default date format depending on date type
      \$('input[name=\"params[date_format]\"]')
        .val(\$(this)
        .find('option:selected')
        .data('format'));
    });
    // set default format
    \$('select[name=\"params[date_type]\"]').trigger('change');
  });
<{{!}}/script>";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "form/templatesLegacy/settings/date_types.hbs";
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
        return array (  73 => 12,  65 => 10,  59 => 9,  53 => 6,  50 => 5,  46 => 4,  41 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/settings/date_types.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/settings/date_types.hbs");
    }
}
