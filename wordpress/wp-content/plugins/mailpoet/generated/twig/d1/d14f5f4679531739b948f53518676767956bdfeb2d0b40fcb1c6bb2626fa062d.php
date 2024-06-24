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

/* form/templatesLegacy/settings/date_formats.hbs */
class __TwigTemplate_d75fd9f344111f9ecb1b3324b4a107ab6837421ef23804c7930ed215aa791542 extends Template
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
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["date_formats"] ?? null));
        foreach ($context['_seq'] as $context["date_type"] => $context["formats"]) {
            // line 2
            yield "  {{#ifCond params.date_type \"===\" \"";
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($context["date_type"], "html", null, true);
            yield "\"}}
    ";
            // line 3
            if ((MailPoetVendor\Twig\Extension\CoreExtension::length($this->env->getCharset(), $context["formats"]) == 1)) {
                // line 4
                yield "      <!-- display format as hidden value -->
      <input type=\"hidden\" name=\"params[date_format]\" value=\"";
                // line 5
                yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape((($__internal_compile_0 = $context["formats"]) && is_array($__internal_compile_0) || $__internal_compile_0 instanceof ArrayAccess ? ($__internal_compile_0[0] ?? null) : null), "html", null, true);
                yield "\" />
    ";
            } else {
                // line 7
                yield "      <!-- display label -->
      <p class=\"clearfix\">
        <label>";
                // line 9
                yield $this->extensions['MailPoet\Twig\I18n']->translate("Order");
                yield "</label>
        <!-- display all possible date formats -->
        <select name=\"params[date_format]\">
          ";
                // line 12
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable($context["formats"]);
                foreach ($context['_seq'] as $context["_key"] => $context["format"]) {
                    // line 13
                    yield "            <option
              {{#ifCond params.date_format \"===\" \"";
                    // line 14
                    yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($context["format"], "html", null, true);
                    yield "\"}}
                selected=\"selected\"
              {{/ifCond}}
              value=\"";
                    // line 17
                    yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($context["format"], "html", null, true);
                    yield "\">";
                    yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($context["format"], "html", null, true);
                    yield "</option>
          ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['format'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 19
                yield "        </select>
      </p>
    ";
            }
            // line 22
            yield "  {{/ifCond}}
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['date_type'], $context['formats'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "form/templatesLegacy/settings/date_formats.hbs";
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
        return array (  95 => 22,  90 => 19,  80 => 17,  74 => 14,  71 => 13,  67 => 12,  61 => 9,  57 => 7,  52 => 5,  49 => 4,  47 => 3,  42 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/settings/date_formats.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/settings/date_formats.hbs");
    }
}
