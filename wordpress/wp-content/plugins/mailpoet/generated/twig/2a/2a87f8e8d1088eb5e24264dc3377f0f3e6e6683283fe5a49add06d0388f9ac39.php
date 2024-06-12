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

/* form/templatesLegacy/blocks/date_years.hbs */
class __TwigTemplate_bb51173393bd115fb73852f25a9771bba45407f3bab35cc8c1403263a5e7b13b extends Template
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
        $context["currentYear"] = \MailPoetVendor\twig_date_format_filter($this->env, "now", "Y");
        // line 2
        $context["minYear"] = (($context["currentYear"] ?? null) - 100);
        // line 3
        echo "
<select id=\"{{ id }}_years\">
  <option value=\"\">";
        // line 5
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Year");
        echo "</option>
  ";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = \MailPoetVendor\twig_ensure_traversable(range(($context["currentYear"] ?? null), ($context["minYear"] ?? null)));
        foreach ($context['_seq'] as $context["_key"] => $context["year"]) {
            // line 7
            echo "    <option
      ";
            // line 8
            if ((($context["currentYear"] ?? null) == $context["year"])) {
                // line 9
                echo "      {{#if params.is_default_today}}selected=\"selected\"{{/if}}
      ";
            }
            // line 11
            echo "    >";
            echo \MailPoetVendor\twig_escape_filter($this->env, $context["year"], "html", null, true);
            echo "</option>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['year'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        echo "</select>";
    }

    public function getTemplateName()
    {
        return "form/templatesLegacy/blocks/date_years.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 13,  62 => 11,  58 => 9,  56 => 8,  53 => 7,  49 => 6,  45 => 5,  41 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/blocks/date_years.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/blocks/date_years.hbs");
    }
}
