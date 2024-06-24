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

/* form/templatesLegacy/blocks/date_days.hbs */
class __TwigTemplate_61cb543be158996f16d5d8b82ef5c705d0385360e195ec90bf04aa95dcdbabb6 extends Template
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
        $context["currentDay"] = $this->extensions['MailPoetVendor\Twig\Extension\CoreExtension']->formatNumber($this->extensions['MailPoetVendor\Twig\Extension\CoreExtension']->formatDate("now", "d"));
        // line 2
        yield "<select id=\"{{ id }}_days\">
  <option value=\"\">";
        // line 3
        yield $this->extensions['MailPoet\Twig\I18n']->translate("Day");
        yield "</option>
  ";
        // line 4
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(range(1, 31));
        foreach ($context['_seq'] as $context["_key"] => $context["day"]) {
            // line 5
            yield "    <option
    ";
            // line 6
            if ((($context["currentDay"] ?? null) == $context["day"])) {
                // line 7
                yield "      {{#if params.is_default_today}}selected=\"selected\"{{/if}}
    ";
            }
            // line 9
            yield "    >";
            yield $this->env->getRuntime('MailPoetVendor\Twig\Runtime\EscaperRuntime')->escape($context["day"], "html", null, true);
            yield "</option>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['day'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 11
        yield "</select>";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "form/templatesLegacy/blocks/date_days.hbs";
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
        return array (  69 => 11,  60 => 9,  56 => 7,  54 => 6,  51 => 5,  47 => 4,  43 => 3,  40 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "form/templatesLegacy/blocks/date_days.hbs", "/home/circleci/mailpoet/mailpoet/views/form/templatesLegacy/blocks/date_days.hbs");
    }
}
