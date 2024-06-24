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
        $context["currentYear"] = $this->extensions['MailPoetVendor\Twig\Extension\CoreExtension']->formatDate("now", "Y");
        // line 2
        $context["minYear"] = (($context["currentYear"] ?? null) - 100);
        // line 3
        yield "
<select id=\"{{ id }}_years\">
  <option value=\"\">";
        // line 5
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Year");
        yield "</option>
  ";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(range(($context["currentYear"] ?? null), ($context["minYear"] ?? null)));
        foreach ($context['_seq'] as $context["_key"] => $context["year"]) {
            // line 7
            yield "    <option
      ";
            // line 8
            if ((($context["currentYear"] ?? null) == $context["year"])) {
                // line 9
                yield "      {{#if params.is_default_today}}selected=\"selected\"{{/if}}
      ";
            }
            // line 11
            yield "    >";
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($context["year"], "html", null, true);
            yield "</option>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['year'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        yield "</select>";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "form/templatesLegacy/blocks/date_years.hbs";
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
        return array (  72 => 13,  63 => 11,  59 => 9,  57 => 8,  54 => 7,  50 => 6,  46 => 5,  42 => 3,  40 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/blocks/date_years.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/blocks/date_years.hbs");
    }
}
