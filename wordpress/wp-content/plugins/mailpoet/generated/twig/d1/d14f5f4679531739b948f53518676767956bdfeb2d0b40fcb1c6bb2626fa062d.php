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
        $context['_seq'] = \MailPoetVendor\twig_ensure_traversable(($context["date_formats"] ?? null));
        foreach ($context['_seq'] as $context["date_type"] => $context["formats"]) {
            // line 2
            echo "  {{#ifCond params.date_type \"===\" \"";
            echo \MailPoetVendor\twig_escape_filter($this->env, $context["date_type"], "html", null, true);
            echo "\"}}
    ";
            // line 3
            if ((\MailPoetVendor\twig_length_filter($this->env, $context["formats"]) == 1)) {
                // line 4
                echo "      <!-- display format as hidden value -->
      <input type=\"hidden\" name=\"params[date_format]\" value=\"";
                // line 5
                echo \MailPoetVendor\twig_escape_filter($this->env, (($__internal_compile_0 = $context["formats"]) && is_array($__internal_compile_0) || $__internal_compile_0 instanceof ArrayAccess ? ($__internal_compile_0[0] ?? null) : null), "html", null, true);
                echo "\" />
    ";
            } else {
                // line 7
                echo "      <!-- display label -->
      <p class=\"clearfix\">
        <label>";
                // line 9
                echo $this->extensions['MailPoet\Twig\I18n']->translate("Order");
                echo "</label>
        <!-- display all possible date formats -->
        <select name=\"params[date_format]\">
          ";
                // line 12
                $context['_parent'] = $context;
                $context['_seq'] = \MailPoetVendor\twig_ensure_traversable($context["formats"]);
                foreach ($context['_seq'] as $context["_key"] => $context["format"]) {
                    // line 13
                    echo "            <option
              {{#ifCond params.date_format \"===\" \"";
                    // line 14
                    echo \MailPoetVendor\twig_escape_filter($this->env, $context["format"], "html", null, true);
                    echo "\"}}
                selected=\"selected\"
              {{/ifCond}}
              value=\"";
                    // line 17
                    echo \MailPoetVendor\twig_escape_filter($this->env, $context["format"], "html", null, true);
                    echo "\">";
                    echo \MailPoetVendor\twig_escape_filter($this->env, $context["format"], "html", null, true);
                    echo "</option>
          ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['format'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 19
                echo "        </select>
      </p>
    ";
            }
            // line 22
            echo "  {{/ifCond}}
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['date_type'], $context['formats'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "form/templatesLegacy/settings/date_formats.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  94 => 22,  89 => 19,  79 => 17,  73 => 14,  70 => 13,  66 => 12,  60 => 9,  56 => 7,  51 => 5,  48 => 4,  46 => 3,  41 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/settings/date_formats.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/settings/date_formats.hbs");
    }
}
